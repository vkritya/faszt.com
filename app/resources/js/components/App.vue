<template>
    <Message v-if="appState == 'error'" />
    <Spinner v-else-if="appState == 'init'" />
    <template v-else>
        <!-- Message if appState = 'error' -->
        <ServerSelector v-if="appMode == 'frontend'" v-model="serverId" :servers="viableServers" :disabled="appState == 'testing'"/>
        <button @click="startStop()">{{ appState == 'ready' ? "Start test" : "Stop test" }}</button>
        <Metrics />
        {{ speedtestData }}
    </template>
</template>
<script setup>
import Metrics from './Metrics.vue'
import ServerSelector from './ServerSelector.vue';
import Message from './Message.vue';
import Spinner from './Spinner.vue';
import { ref, watch } from 'vue'

// Determine mode
const appMode = ref('');
if (SPEEDTEST_SERVERS.length == 0) {
    appMode.value = 'standalone'
} else {
    appMode.value = 'frontend' // or 'dual', but from the frontend's point of view they don't differ
}

// Define appState
const appState = ref('init');

// Define error handling
const errorMessage = ref('')
const setError = (message) => {
    appState.value = 'error'
    errorMessage.value = message
}

// Define speedtest object
const speedtest = new Speedtest()
speedtest.setParameter("getIp_ispInfo", false);

// Get default server
const viableServers = ref([])
if (appMode.value == 'frontend') {
    // Load SPEEDTEST_SERVERS into speedtest
    if (typeof SPEEDTEST_SERVERS === "string") {
        // Load servers.json from URL
        speedtest.loadServerList(SPEEDTEST_SERVERS, (servers) => {
            if (servers == null) {
                // Failed to load server list
                setError("Failed to load server list from URL.")
            } else {
                // Server list loaded into speedtest, update servers.json in variable
                SPEEDTEST_SERVERS = servers
            }
        });
    } else {
        // Load servers.json
        s.addTestPoints(SPEEDTEST_SERVERS)
    }
    if (appState != 'error') {
        // Find best server from servers
        speedtest.selectServer((server) => {
            if (server != null) {
                // Populate server list for manual selection, app is ready to start
                for (var i = 0; i < SPEEDTEST_SERVERS.length; i++) {
                    // Skip servers that didn't respond to ping
                    if (SPEEDTEST_SERVERS[i].pingT == -1)
                        continue
                    viableServers.value += {
                        'index': i,
                        'name': SPEEDTEST_SERVERS[i].name,
                        'selected': SPEEDTEST_SERVERS[i] === server
                    }
                }
                appState.value = 'ready'
            } else {
                setError("Server list loaded, but no servers are available.")
            }
        })
    }
} else {
    // If running in standalone mode, there's no need to ping the server, app is ready
    appState.value = 'ready'
}

// Define ref for server selector and watch for changes
const serverId = ref();
watch(serverId, (newValue, oldValue) => {
    // Synchronize change to speedtest object
    speedtest.setSelectedServer(SPEEDTEST_SERVERS[newValue])
})

// Define metric data container
const speedtestData = ref(null);
const startStop = () => {
    if (speedtest.getState() == 3) {
        // If test is running currently, abort
        speedtest.abort()
    } else {
        // Else, bind callbacks and start
        speedtest.onupdate = (data) => {
            speedtestData.value = data
        }
        speedtest.onend = (aborted) => {
            // If ended because of abort, reset data
            if (aborted)
                speedtestData.value = null
            // App is ready
            appState.value = 'ready'
        }
        speedtest.start()
        appState.value = 'testing'
    }
}
</script>
<style scoped></style>