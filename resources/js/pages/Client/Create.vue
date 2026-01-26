<script setup lang="ts">
import ClientController from '@/actions/App/Http/Controllers/ClientController';
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { index } from '@/routes/clients';
import type { BreadcrumbItem } from '@/types';
import { Form, Head } from '@inertiajs/vue3';
import { ref } from 'vue';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
    {
        title: 'Clients',
        href: index().url,
    },
    {
        title: 'Create',
        href: '#',
    },
];

const isActive = ref(false);

const formFields = ref([
    { id: 'name', label: 'Name', inputType: 'text' },
    { id: 'email', label: 'Email', inputType: 'email' },
    { id: 'phone', label: 'Phone', inputType: 'tel' },
    { id: 'company', label: 'Company', inputType: 'text' },
    { id: 'address', label: 'Address', inputType: 'text' },
    { id: 'status', label: 'Status', inputType: 'toggle' },
]);
</script>


<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div
            class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4"
        >
            <div class="card">
                <Form
                    class="grid gap-4"
                    :action="ClientController.store()"
                    #default="{ errors }"
                >
                    <div
                        v-for="formField in formFields"
                        :key="formField.id"
                        class="flex flex-col gap-1"
                    >
                        <div v-if="formField.inputType === 'toggle'">
                            <div class="flex flex-col gap-1">
                                <label
                                    for="status"
                                    class="block text-sm/6 font-medium text-gray-900"
                                    >Status</label
                                >
                                <input
                                    type="hidden"
                                    name="status"
                                    :value="isActive ? 'active' : 'inactive'"
                                />
                                <div class="flex items-center gap-1">
                                    <ToggleSwitch v-model="isActive" />
                                    <span>{{
                                        isActive ? 'active' : 'inactive'
                                    }}</span>
                                </div>
                            </div>
                        </div>
                        <div v-else>
                            <div class="flex flex-col gap-1">
                                <label
                                    :for="formField.id"
                                    class="block text-sm/6 font-medium text-gray-900"
                                    >{{ formField.label }}</label
                                >
                                <input
                                    :id="formField.id"
                                    :type="formField.inputType"
                                    :name="formField.id"
                                    class="border p-1"
                                    :class="{
                                        'border-red-500': errors[formField.id],
                                    }"
                                />
                                <p class="text-xs text-red-500 italic">
                                    {{ errors[formField.id] }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <Button type="submit">Create User</Button>
                </Form>
            </div>
        </div>
    </AppLayout>
</template>
