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

interface ProjectCreateProps {
    users: {
        data: User[];
    };
    clients: {
        data: Client[];
    };
}

defineProps<ProjectCreateProps>();

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
                            for="client"
                            class="block text-sm/6 font-medium text-gray-900"
                        >
                            Client
                        </label>
                        <input
                            id="client"
                            type="text"
                            name="client"
                            class="border p-1"
                            :class="{
                                'border-red-500': errors['client'],
                            }"
                        />
                        <p class="text-xs text-red-500 italic">
                            {{ errors['client'] }}
                        </p>
                    </div>

                    <div class="flex flex-col gap-1">
                        <label
                            for="user"
                            class="block text-sm/6 font-medium text-gray-900"
                        >
                            User
                        </label>
                        <input
                            id="user"
                            type="text"
                            name="user"
                            class="border p-1"
                            :class="{
                                'border-red-500': errors['user'],
                            }"
                        />
                        <p class="text-xs text-red-500 italic">
                            {{ errors['user'] }}
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
                            class="border p-1"
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

                    <Button type="submit">Create Project</Button>
                </Form>
            </div>
        </div>
    </AppLayout>
</template>
