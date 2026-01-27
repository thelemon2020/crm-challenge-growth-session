<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
    {
        title: 'Projects',
        href: '#',
    },
];

interface ProjectIndexProps {
    projects: {
        data: any[];
    };
}

const props = defineProps<ProjectIndexProps>();
console.log(props.projects);
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div
            class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4"
        >
            <div class="card grid gap-4">
                <Button
                    as="a"
                    href="#"
                    class="w-fit"
                    label="Add Project"
                    icon="pi pi-plus"
                />

                <DataTable
                    :value="projects.data"
                    :dt="{
                        headerCell: {
                            background: '{surface.800}',
                            color: '{surface.100}',
                        },
                    }"
                >
                    <Column field="title" header="Title" :sortable="true" />
                    <Column
                        field="description"
                        header="Description"
                        :sortable="true"
                    ></Column>
                </DataTable>
            </div>
        </div>
    </AppLayout>
</template>
