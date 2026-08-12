<template>
    <div class="flex justify-center">
        <button type="button" class="bg-blue-600" @click="createPdf">Создать pdf</button>
    </div>
</template>

<script setup>
import { useAuthStore } from '../stores/authStore';

const authStore = useAuthStore();

const createPdf = async () => {
    const result = await authStore.createPdf();

    if(result.success)
    {
        console.log('good');
    }
    else
    {
        console.log(result.error);
    }
}
</script>