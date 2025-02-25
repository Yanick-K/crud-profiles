<script setup>
import TextInput from "@/Components/TextInput.vue";
import NeutralLayout from "@/Layouts/NeutralLayout.vue";
import {Link} from "@inertiajs/vue3";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import {defineComponent, ref} from "vue";
import {useForm} from "@inertiajs/vue3";


const props = defineProps({
    profile: {
        type: Object,
    },
    storageUrl: {
        type: String,
    }
})

defineComponent({
    components: {PrimaryButton, InputLabel, Link, InputError, TextInput, NeutralLayout}
})

const form = useForm({
    action: 'update',
    first_name: props.profile.data.first_name,
    last_name: props.profile.data.last_name,
    status: props.profile.data.status,
    image: props.profile.data.image,
});

const previewUrl = ref('');

const isSaved = ref(false);

if (props.profile.data.image !== null) {
    previewUrl.value =  props.storageUrl + '/' + props.profile.data.image;
}

function handleImageUpload(event) {
    form.image = event.target.files[0];
    previewUrl.value = URL.createObjectURL(form.image);
}

function submit() {
    const formData = new FormData();

    formData.append('_method', 'put');
    formData.append('action', 'update');
    formData.append('first_name', form.first_name);
    formData.append('last_name', form.last_name);
    formData.append('status', form.status);
    formData.append('image', form.image);

    axios.post('/api/profiles/' + props.profile.data.id, formData).then((response) => {
        if (response.status === 200) {
            isSaved.value = true;
        }
    }).catch((error) => console.error(error))
}
</script>

<template>
    <NeutralLayout>
        <div v-if="isSaved" id="toast-success" class=" mx-auto flex items-center w-full max-w-xs p-4 mb-4 text-gray-500 bg-white rounded-lg shadow-sm dark:text-gray-400 dark:bg-gray-800 border border-gray-700 shadow-sm" role="alert">
            <div class="inline-flex items-center justify-center shrink-0 w-8 h-8 text-green-500 bg-green-100 rounded-lg dark:bg-green-800 dark:text-green-200">
                <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 8.207-4 4a1 1 0 0 1-1.414 0l-2-2a1 1 0 0 1 1.414-1.414L9 10.586l3.293-3.293a1 1 0 0 1 1.414 1.414Z"/>
                </svg>
                <span class="sr-only">Check icon</span>
            </div>
            <div class="ms-3 text-sm font-normal">Item saved successfully.</div>
            <button type="button" @click="isSaved = false" class="ms-auto -mx-1.5 -my-1.5 bg-white text-gray-400 hover:text-gray-900 rounded-lg focus:ring-2 focus:ring-gray-300 p-1.5 hover:bg-gray-100 inline-flex items-center justify-center h-8 w-8 dark:text-gray-500 dark:hover:text-white dark:bg-gray-800 dark:hover:bg-gray-700" data-dismiss-target="#toast-success" aria-label="Close">
                <span class="sr-only">Close</span>
                <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                </svg>
            </button>
        </div>
        <Link :href="route('dashboard')">

            <PrimaryButton type="button" class="text-color-white">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 mr-1">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                </svg>
                Back
            </PrimaryButton>
        </Link>

        <h1 class="text-white text-xl text-center mb-5">Edit a profil</h1>
        <form @submit.prevent="submit">
            <div>
                <InputLabel for="first_name" value="First Name" />

                <TextInput
                    id="first_name"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.first_name"
                    required
                    autofocus
                    autocomplete="username"
                />

                <InputError class="mt-2" :message="form.errors.first_name" />
            </div>

            <div class="mt-4">
                <InputLabel for="last_name" value="Last Name" />

                <TextInput
                    id="last_name"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.last_name"
                    required
                    autocomplete="current-password"
                />

                <InputError class="mt-2" :message="form.errors.last_name" />
            </div>
            <div class="mt-4">
                <label for="status" class="sr-only">Choisir un statut</label>
                <select v-model="form.status" class="block py-2.5 px-0 w-full text-sm text-gray-500 bg-transparent border-0 border-b-2 border-gray-200 appearance-none dark:text-gray-400 dark:border-gray-700 focus:outline-none focus:ring-0 focus:border-gray-200 peer">
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                    <option value="waiting">Waiting</option>
                </select>
                <InputError class="mt-2" :message="form.errors.status" />
            </div>

            <div class="mt-4">
                <input @change="handleImageUpload" accept=".jpg, .jpeg, .png, .gif" type="file" id="file" name="file" />
                <img v-if="form.image" :src="previewUrl" alt="Preview" style="max-width: 100%;" />
                <InputError class="mt-2" :message="form.errors.image" />
            </div>


            <div class="mt-4 flex items-center justify-end">
                <button
                    class="hover:bg-green-950 bg-green-800 px-3 py-1 rounded-md flex mb-1 text-xs font-semibold uppercase tracking-widest text-white mx-auto"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Save
                </button>
            </div>
        </form>
    </NeutralLayout>
</template>

<style scoped>

</style>
