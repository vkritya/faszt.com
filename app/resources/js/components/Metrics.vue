<template>
    <Indickator />
    <!--  Indickator   |       DOWNLOAD    |    UPLOAD      -->
    <!--  Indickator   |   IPAddress  |   Ping   |  Jitter  -->
</template>

<script setup>
import Indickator from './metrics/Indickator.vue';

function mbpsToAmount(s) {
    return 1 - (1 / (Math.pow(1.3, Math.sqrt(s))));
}
function format(d) {
    d = Number(d);
    if (d < 10) return d.toFixed(2);
    if (d < 100) return d.toFixed(1);
    return d.toFixed(0);
}

function oscillate() {
    return 1 + 0.02 * Math.sin(Date.now() / 100);
}

</script>

<style scoped></style>