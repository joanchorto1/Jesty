<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { defineProps, reactive, watchEffect } from 'vue';
import { Inertia } from "@inertiajs/inertia";
import SaveIcon from "@/Components/Icons/SaveIcon.vue";

const props = defineProps({
    company: Object,
    public_key: String,
    private_key: String,
});

// Reactive form object
const form = reactive({
    public_key: props.public_key,
    private_key: props.private_key,
});



const saveKeys = () => {
    console.log(form);
    return Inertia.put(route('companies.updateKeys', props.company.id), form);
};

const generateKeys = () => {
    console.log('Generating new keys');
    return Inertia.get(route('companies.generateKeys', props.company.id));
};
</script>

<template>
    <AppLayout>
        <div class="p-6 bg-white shadow rounded-lg">
            <h2 class="text-xl font-bold mb-4">Manage Company Keys</h2>

            <form @submit.prevent="saveKeys">
                <!-- Public Key Field -->
                <div class="mb-4">
                    <label for="public_key" class="block text-gray-700 font-medium mb-2">Public Key</label>
                    <textarea
                        id="public_key"
                        v-model="form.public_key"
                        class="w-full p-3 border rounded-lg focus:ring focus:ring-blue-300"
                        rows="6"
                        placeholder="Enter public key here"
                    ></textarea>
                </div>

                <!-- Private Key Field -->
                <div class="mb-4">
                    <label for="private_key" class="block text-gray-700 font-medium mb-2">Private Key</label>
                    <textarea
                        id="private_key"
                        v-model="form.private_key"
                        class="w-full p-3 border rounded-lg focus:ring focus:ring-blue-300"
                        rows="6"
                        placeholder="Enter private key here"
                    ></textarea>
                </div>

                <!-- Submit Button -->
                <div class="text-right">
                    <button
                        type="submit"
                        class="px-6 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 focus:outline-none focus:ring"
                    >
                        <SaveIcon class="w-6 h-6 fill-white stroke-gray-400" />
                    </button>
                </div>

                <!-- Generate Keys Button -->
                <div>
                    <button
                        type="button"
                        @click="generateKeys"
                        class="px-6 py-2 text-gray-500 hover:border-b hover:pb-2 hover:text-blue-500 focus:outline-none focus:ring"
                    >
                        No tengo claves, generar nuevas
                    </button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>

<style scoped>
</style>
