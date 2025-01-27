<template>
    <div class="flex flex-col md:grid md:grid-cols-[minmax(200px,_4fr)_6fr] gap-8 px-4 w-full md:pt-12">
        <Indickator :speed="indicatedSpeed" :color="props.data?.testState == 1 && indicatedSpeed > 0 ?
            'fill-primary stroke-primary'
            : (props.data?.testState == 3 && indicatedSpeed > 0 ?
                'fill-secondary stroke-secondary'
                : '')" />
        <div class="flex flex-col items-center justify-center gap-2">
            <slot></slot>
            <div class="stats bg-base-200 shadow grid-cols-2 w-full">
                <div class="stat place-items-center text-primary w-full">
                    <div class="stat-title">Download</div>
                    <div class="stat-value">{{ avgDownload ? `${format(avgDownload)}` : '-' }}</div>
                    <div class="stat-actions w-full">
                        <progress class="progress progress-primary w-full" :value="data?.dlProgress"></progress>
                    </div>
                </div>
                <div class="stat place-items-center text-secondary w-full">
                    <div class="stat-title">Upload</div>
                    <div class="stat-value">
                        {{ avgUpload ? `${format(avgUpload)}` : '-' }}
                    </div>
                    <div class="stat-actions w-full">
                        <progress class="progress progress-secondary w-full" :value="data?.ulProgress"></progress>
                    </div>
                </div>
            </div>
            <div class="stats bg-base-200 shadow grid-cols-2 w-full">
                <div v-if="data?.clientIp" class="stat place-items-center">
                    <div class="stat-title">Ping</div>
                    <div class="stat-value">{{ data?.clientIp ?
                        `${data.clientIp.split(' ')[0]} ms` : '-' }}</div>
                </div>
                <div class="stat place-items-center">
                    <div class="stat-title">Ping</div>
                    <div class="stat-value">{{ data?.pingStatus ?
                        `${Number(data.pingStatus).toFixed(0)} ms` : '-' }}</div>
                </div>
                <div class="stat place-items-center">
                    <div class="stat-title">Jitter</div>
                    <div class="stat-value">{{ data?.jitterStatus ?
                        `${Number(data.jitterStatus).toFixed(2)} ms` : '-' }}</div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed, defineProps, ref, watch } from 'vue';
import Indickator from './metrics/Indickator.vue';

const props = defineProps(["data"])

function format(d) {
    d = Number(d);
    if (d < 10)
        return `${d.toFixed(2)} Mbps`;
    if (d < 100)
        return `${d.toFixed(1)} Mbps`;
    if (d < 1000)
        return `${d.toFixed(0)} Mbps`;
    return `${(d / 1000).toFixed(2)} Gbps`;
}

const dlValues = ref([])
const ulValues = ref([])

// Aggregate download speeds
watch(() => props.data?.dlStatus, (newValue, oldValue) => {
    dlValues.value.push(Number(newValue))
})
const avgDownload = computed(
    () => {
        return dlValues.value.reduce((accumulator, currentValue) => {
            return accumulator + currentValue
        }, 0) / dlValues.value.length
    }
)

// Aggregate upload speeds
watch(() => props.data?.ulStatus, (newValue, oldValue) => {
    ulValues.value.push(Number(newValue))
})
const avgUpload = computed(
    () => {
        return ulValues.value.reduce((accumulator, currentValue) => {
            return accumulator + currentValue
        }, 0) / ulValues.value.length
    }
)

// Reset aggregation on start of new measurement
watch(() => props.data?.testState, (newValue, oldValue) => {
    if (newValue == 2) {
        dlValues.value = []
        ulValues.value = []
    }
})

const indicatedSpeed = computed(() => {
    switch (props.data?.testState) {
        case 1:
            return props.data.dlStatus
        case 3:
            return props.data.ulStatus
    }
    return 0
})
</script>

<style scoped></style>