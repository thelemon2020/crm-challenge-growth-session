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
                    <div class="flex flex-col gap-1">
                        <label
                            for="name"
                            class="block text-sm/6 font-medium text-gray-900"
                        >
                            Name
                        </label>
                        <input
                            id="name"
                            type="text"
                            name="name"
                            class="border p-1"
                            :class="{
                                'border-red-500': errors['name'],
                            }"
                        />
                        <p class="text-xs text-red-500 italic">
                            {{ errors['name'] }}
                        </p>
                    </div>

                    <div class="flex flex-col gap-1">
                        <label
                            for="email"
                            class="block text-sm/6 font-medium text-gray-900"
                        >
                            Email
                        </label>
                        <input
                            id="email"
                            type="email"
                            name="email"
                            class="border p-1"
                            :class="{
                                'border-red-500': errors['email'],
                            }"
                        />
                        <p class="text-xs text-red-500 italic">
                            {{ errors['email'] }}
                        </p>
                    </div>

                    <div class="flex flex-col gap-1">
                        <label
                            for="phone"
                            class="block text-sm/6 font-medium text-gray-900"
                        >
                            Phone
                        </label>
                        <input
                            id="phone"
                            type="tel"
                            name="phone"
                            class="border p-1"
                            :class="{
                                'border-red-500': errors['phone'],
                            }"
                        />
                        <p class="text-xs text-red-500 italic">
                            {{ errors['phone'] }}
                        </p>
                    </div>

                    <div class="flex flex-col gap-1">
                        <label
                            for="company"
                            class="block text-sm/6 font-medium text-gray-900"
                        >
                            Company
                        </label>
                        <input
                            id="company"
                            type="text"
                            name="company"
                            class="border p-1"
                            :class="{
                                'border-red-500': errors['company'],
                            }"
                        />
                        <p class="text-xs text-red-500 italic">
                            {{ errors['company'] }}
                        </p>
                    </div>

                    <div class="flex flex-col gap-1">
                        <label
                            for="address"
                            class="block text-sm/6 font-medium text-gray-900"
                        >
                            Address
                        </label>
                        <input
                            id="address"
                            type="text"
                            name="address"
                            class="border p-1"
                            :class="{
                                'border-red-500': errors['address'],
                            }"
                        />
                        <p class="text-xs text-red-500 italic">
                            {{ errors['address'] }}
                        </p>
                    </div>

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
                            <span>{{ isActive ? 'active' : 'inactive' }}</span>
                        </div>
                    </div>

                    <Button type="submit">Create User</Button>
                </Form>
            </div>
        </div>
    </AppLayout>
</template>
