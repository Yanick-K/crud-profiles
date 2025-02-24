<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head, Link} from '@inertiajs/vue3';
import {ref} from "vue";

const props = defineProps({
    isAuthenticated: {
        type: Boolean,
    },
    storageUrl: {
        type: String,
    }
});

const profiles = ref([]);

axios.get('api/profiles').then((response) => {
    profiles.value = response.data.data;
    console.log(profiles.value);
}).catch(error => console.error(error))

function getImage(path) {
    return props.storageUrl + '/' + path;
}

function deleteProfile(profileId) {
    if (!confirm('Want to delete this item ?')) {
        return;
    }
    isLoading.value = true;
    axios.delete('api/profiles/' + profileId).then((response) => {
        if (response.status === 200) {
            //show toast
            //supprimer dans la liste des profiles
            //profiles.splice()
        }
    }).catch(error => console.error(error)).finally(() => isLoading.value = false);
}

</script>

<template>
    <Head title="Dashboard"/>
    <AuthenticatedLayout>
        <template #header>
            <h2
                class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200"
            >
                Dashboard
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div
                    class="overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800"
                >
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <Link :href="route('profile.create')">
                            <button v-if="isAuthenticated"
                                    class="ml-auto hover:bg-green-950 bg-green-800 px-2 py-1 rounded-md flex mb-1"
                                    type="button">
                                <svg style="margin-top: 1px" xmlns="http://www.w3.org/2000/svg" fill="none"
                                     viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                     class="size-5 text-center items-center">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                                </svg>
                                Create
                            </button>
                        </Link>
                        <div class="relative overflow-x-auto">
                            <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                                <thead
                                    class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                <tr>
                                    <th scope="col" class="px-6 py-3">
                                        Last name
                                    </th>
                                    <th scope="col" class="px-6 py-3">
                                        First name
                                    </th>
                                    <th v-if="isAuthenticated" scope="col" class="px-6 py-3">
                                        Status
                                    </th>
                                    <th scope="col" class="px-6 py-3">
                                        Image
                                    </th>
                                    <th v-if="isAuthenticated" scope="col" class="px-6 py-3">
                                        Actions
                                    </th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="profile in profiles" :key="profile.id"
                                    class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 border-gray-200">
                                    <th scope="row"
                                        class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                        {{ profile.last_name }}
                                    </th>
                                    <td class="px-6 py-4">
                                        {{ profile.first_name }}
                                    </td>
                                    <td v-if="isAuthenticated" class="px-6 py-4">
                                        {{ profile.status }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <img v-if="profile.image !== null" :src="getImage(profile.image)"
                                             alt="profile image" width="200"/>
                                    </td>
                                    <td v-if="isAuthenticated">
                                        <Link :href="route('profile.edit')">
                                            <button type="button">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                     stroke-width="1.5" stroke="currentColor" class="size-6">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                          d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10"/>
                                                </svg>
                                            </button>
                                        </Link>
                                        <button type="button" @click="deleteProfile(profile.id)">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                 stroke-width="1.5" stroke="currentColor" class="size-6 text-red-700">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                      d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/>
                                            </svg>
                                        </button>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
