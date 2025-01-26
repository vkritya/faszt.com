<template>
    <Indickator :speed="indicatedSpeed" />
    <!--  Indickator   |       DOWNLOAD    |    UPLOAD      -->
    <!--  Indickator   |   IPAddress  |   Ping   |  Jitter  -->
</template>

<script setup>
import { computed, defineProps } from 'vue';
import Indickator from './metrics/Indickator.vue';

const props = defineProps(["data"])

function format(d) {
    d = Number(d);
    if (d < 10) return d.toFixed(2);
    if (d < 100) return d.toFixed(1);
    return d.toFixed(0);
}



const indicatedSpeed = computed(() => {
    switch (props.data?.testState) {
        case 1:
            return props.data.dlStatus
        case 3:
            return props.data.ulStatus
        default:
            return 0
    }
})
</script>

<style scoped></style>