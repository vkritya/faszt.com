<template>
    <div>
        Select server:
        <select v-model="model">
            <option v-for="server in servers" :value="server.index" :selected="server.selected">{{ server.name }}</option>
        </select>
    </div>
</template>

<script setup>
const model = defineModel()
const props = defineProps(["servers"])
</script>

<style scoped></style>