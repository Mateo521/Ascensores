<script setup>
import SignaturePad from 'signature_pad'
import { onMounted, ref } from 'vue'

const props = defineProps({
  modelValue: String,        // base64
  width: { type: Number, default: 500 },
  height: { type: Number, default: 180 },
  penColor: { type: String, default: '#000' },
})
const emit = defineEmits(['update:modelValue'])

const canvasRef = ref(null)
let pad

onMounted(() => {
  pad = new SignaturePad(canvasRef.value, { penColor: props.penColor })
  if (props.modelValue) {
    const img = new Image()
    img.onload = () => {
      const ctx = canvasRef.value.getContext('2d')
      ctx.clearRect(0,0,canvasRef.value.width, canvasRef.value.height)
      ctx.drawImage(img, 0, 0, canvasRef.value.width, canvasRef.value.height)
    }
    img.src = props.modelValue
  }
})

function clearPad() {
  pad.clear()
  emit('update:modelValue', '')
}
function save() {
  const dataUrl = pad.toDataURL('image/png') // base64
  emit('update:modelValue', dataUrl)
}
</script>

<template>
  <div class="space-y-2">
    <canvas :width="width" :height="height" ref="canvasRef" class="border rounded bg-white"></canvas>
    <div class="flex gap-2">
      <button type="button" class="px-3 py-1 border rounded" @click="clearPad">Limpiar</button>
      <button type="button" class="px-3 py-1 border rounded bg-gray-50" @click="save">Guardar firma</button>
    </div>
  </div>
</template>
