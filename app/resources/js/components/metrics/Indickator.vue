<template>
    <div>
        <svg width="40%" viewBox="0 0 234.378 206.6457">
            <ellipse cx="46.5354" cy="156.3307" rx="46.5354" ry="50.315" />
            <path ref="indickator" class="st0" d="M43.2366,127.3113c54.384,0.0889,108.1335,6.0245,150.0863,40.8777" />
        </svg>
    </div>
</template>

<script setup>
import anime from 'animejs';
import { useTemplateRef, onMounted, ref, watch, computed, defineProps } from 'vue'

const indickator = useTemplateRef('indickator')
var morph = null

const props = defineProps(["speed"])

// Define setpoint and current value
const setpoint = computed(() => {
    if (props.speed == null || props.speed <= 0)
        return 0
    else
        return 1 / (1 + Math.pow(Math.E, - Math.log10(props.speed)) * 5)
})
let current = 0;

// Define system parameters
const inertia = 0.25;
const damping = 0.01;
const Kp = 1.2;
const Ki = 0.1;
const Kd = 0.6;

let lastTime = null;
let lastError = 0;
let errorSum = 0;
let plantSpeed = 0;
const driverLoop = (currentTime) => {
    if (lastTime != null) {
        let timeDelta = (currentTime - lastTime) / 1000;  // div to convert to seconds
        // If timeDelta is too high, don't do physics this frame
        // Probably too high because page was sent to background and regained focus later
        // requestAnimationFrame pauses while page is in background
        if (timeDelta < 0.2) {
            // Calculate error
            let error = setpoint.value - current
            // PID
            let signal = Kp * error
            signal += Ki * errorSum * timeDelta / 2
            signal += Kd * (error - lastError) / timeDelta
            // Plant is mass with damping
            plantSpeed = (1 - damping) * (plantSpeed + signal * timeDelta / inertia)
            current += plantSpeed * timeDelta
            // Add a bit of scaled noise to the animation
            current *= 1 + 0.005 * (Math.random()-0.5)
            // Set svg progress, 0-1 should map to 0.15-0.85 to allow for overshoots
            morph.seek((current * (0.85 - 0.15) + 0.15) * morph.duration)

            lastError = error
            errorSum += error
        }
    }
    // Set current time
    lastTime = currentTime
    // TODO: Cleanup on component destroy/unmount
    requestAnimationFrame(driverLoop)
}

onMounted(() => {
    // Create svg interpolator
    morph = anime({
        targets: indickator.value,
        keyframes: [
            { d: 'M43.2366,127.3113c54.384,0.0889,96.7949,13.2871,126.6532,57.8855', duration: 0 },
            { d: 'M43.2366,127.3113c54.384,0.0889,108.1335,6.0245,150.0863,40.8777' },
            { d: 'M43.2366,127.3113c54.384,0.0889,118.1634-2.0917,164.6516,7.3571' },
            { d: 'M43.2366,127.3113c54.384,0.0889,124.1716-12.8178,162.1808-30.5554' },
            { d: 'M43.2366,127.3113c54.384,0.0889,117.9895-39.6608,146.4957-70.4294' },
            { d: 'M43.2366,127.3113c54.384,0.0889,104.796-63.0417,114.3715-103.8782' },
            { d: 'M43.2366,127.3113c54.384,0.0889,81.1733-77.4033,86.5983-118.9951' },
        ],
        easing: 'linear',
        autoplay: false,
    })
    // Set to "zero" (0 is at 0.15 and 1 is at 0.85 to allow for overshoots)
    morph.seek(0.15 * morph.duration)
    // Begin animation loop
    requestAnimationFrame(driverLoop)
})
</script>

<style scoped>
svg {
    overflow: visible;
}

.st0 {
    fill: none;
    stroke: #000000;
    stroke-width: 42.5197;
    stroke-linecap: round;
    stroke-miterlimit: 10;
}
</style>