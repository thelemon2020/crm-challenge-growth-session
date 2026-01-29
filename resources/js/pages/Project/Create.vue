<script setup lang="ts">
import ProjectController from '@/actions/App/Http/Controllers/ProjectController';
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import type { BreadcrumbItem, Client, User } from '@/types';
import { Form, Head } from '@inertiajs/vue3';
import { ref } from 'vue';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
    {
        title: 'Projects',
        href: ProjectController.index().url,
    },
    {
        title: 'Create',
        href: '#',
    },
];

enum ProjectStatus {
    Pending = 'pending',
    InProgress = 'in_progress',
    OnHold = 'on_hold',
    Review = 'review',
    Completed = 'completed',
    Cancelled = 'cancelled',
}

interface ProjectCreateProps {
    users: {
        data: User[];
    };
    clients: {
        data: Client[];
    };
    status: ProjectStatus;
}

const props = defineProps<ProjectCreateProps>();
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
                    :action="ProjectController.store()"
                    #default="{ errors }"
                >
                    <div class="flex flex-col gap-1">
                        <label
                            for="title"
                            class="block text-sm/6 font-medium text-gray-900"
                        >
                            Title
                        </label>
                        <input
                            id="title"
                            type="text"
                            name="title"
                            class="border p-1"
                            :class="{
                                'border-red-500': errors['title'],
                            }"
                        />
                        <p class="text-xs text-red-500 italic">
                            {{ errors['title'] }}
                        </p>
                    </div>

                    <div class="flex flex-col gap-1">
                        <label
                            for="description"
                            class="block text-sm/6 font-medium text-gray-900"
                        >
                            Description
                        </label>
                        <input
                            id="description"
                            type="text"
                            name="description"
                            class="border p-1"
                            :class="{
                                'border-red-500': errors['description'],
                            }"
                        />
                        <p class="text-xs text-red-500 italic">
                            {{ errors['description'] }}
                        </p>
                    </div>

                    <div class="flex flex-col gap-1">
                        <label
                            for="client_id"
                            class="block text-sm/6 font-medium text-gray-900"
                            >Client</label
                        >
                        <select
                            name="client_id"
                            id="client_id"
                            class="bg-neutral-secondary-medium border-default-medium text-heading rounded-base focus:ring-brand focus:border-brand placeholder:text-body block w-full border px-3 py-2.5 text-sm shadow-xs"
                            :class="{
                                'border-red-500': errors['client_id'],
                            }"
                        >
                            <option selected>Choose a client</option>
                            <option
                                v-for="client in clients.data"
                                :key="client.id"
                                :value="client.id"
                            >
                                {{ client.name }}
                            </option>
                        </select>
                        <p class="text-xs text-red-500 italic">
                            {{ errors['client_id'] }}
                        </p>
                    </div>

                    <div class="flex flex-col gap-1">
                        <label
                            for="user_id"
                            class="block text-sm/6 font-medium text-gray-900"
                        >
                            User
                        </label>
                        <select
                            name="user_id"
                            id="user_id"
                            class="bg-neutral-secondary-medium border-default-medium text-heading rounded-base focus:ring-brand focus:border-brand placeholder:text-body block w-full border px-3 py-2.5 text-sm shadow-xs"
                            :class="{
                                'border-red-500': errors['user_id'],
                            }"
                        >
                            <option selected>Choose a user</option>
                            <option
                                v-for="user in users.data"
                                :key="user.id"
                                :value="user.id"
                            >
                                {{ user.name }}
                            </option>
                        </select>
                        <p class="text-xs text-red-500 italic">
                            {{ errors['user_id'] }}
                        </p>
                    </div>

                    <div class="flex flex-col gap-1">
                        <label
                            for="deadline"
                            class="block text-sm/6 font-medium text-gray-900"
                        >
                            Deadline
                        </label>
                        <input
                            id="deadline"
                            type="date"
                            name="deadline"
                            class="bg-neutral-secondary-medium border-default-medium text-heading rounded-base focus:ring-brand focus:border-brand placeholder:text-body block w-full border px-3 py-2.5 text-sm shadow-xs"
                            :class="{
                                'border-red-500': errors['deadline'],
                            }"
                        />
                        <p class="text-xs text-red-500 italic">
                            {{ errors['deadline'] }}
                        </p>
                    </div>

                    <div class="flex flex-col gap-1">
                        <label
                            for="status"
                            class="block text-sm/6 font-medium text-gray-900"
                            >Status</label
                        >
                        <select
                            name="status"
                            id="status"
                            class="bg-neutral-secondary-medium border-default-medium text-heading rounded-base focus:ring-brand focus:border-brand placeholder:text-body block w-full border px-3 py-2.5 text-sm shadow-xs"
                            :class="{
                                'border-red-500': errors['status'],
                            }"
                        >
                            <option selected>Choose a status</option>
                            <option
                                v-for="stat in status"
                                :key="stat"
                                :value="stat"
                            >
                                {{ stat }}
                            </option>
                        </select>
                        <p class="text-xs text-red-500 italic">
                            {{ errors['status'] }}
                        </p>
                    </div>

                    <Button type="submit">Create Project</Button>
                </Form>
            </div>
        </div>
    </AppLayout>
</template>
