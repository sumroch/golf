<template>
    <span>{{ time }}</span>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'

const props = defineProps({
    timezone: {
        type: String,
        default: 'Asia/Jakarta'
    }
})

const time = ref('')
let timerId

const updateTime = () => {
    time.value = new Date().toLocaleTimeString('id-ID', {
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit',
        timeZone: props.timezone
    })
}

onMounted(() => {
    updateTime()
    timerId = setInterval(updateTime, 1000)
})

onUnmounted(() => {
    clearInterval(timerId)
})
</script>
