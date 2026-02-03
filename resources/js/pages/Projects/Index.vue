<script setup lang="ts">
import ProjectController from '@/actions/App/Http/Controllers/ProjectController';
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import { useConfirm } from 'primevue';

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
    can: {
      delete_project: boolean
    },
    projects: {
        data: any[];
    };
}

const confirm = useConfirm();

defineProps<ProjectIndexProps>();

function deleteProject(id: number) {
    confirm.require({
        message: 'Are you sure you want to delete this project?',
        header: 'Confirmation',
        icon: 'pi pi-exclamation-triangle',
        rejectProps: {
            label: 'Cancel',
            severity: 'secondary',
            outlined: true
        },
        acceptProps: {
            label: 'Delete',
            severity: 'danger'
        },
        accept: () => {
            const projectDestroyRoute = ProjectController.destroy(id);

            router.visit(projectDestroyRoute.url, {
                method: projectDestroyRoute.method,
            });
        }
    })
}
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
                    :href="ProjectController.create().url"
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
                    />
                    <Column
                        field="client.name"
                        header="Client"
                        :sortable="true"
                    />
                    <Column field="title" header="Project" :sortable="true" />
                    <Column field="user.name" header="User" :sortable="true" />
                    <Column field="status" header="Status" :sortable="true" />
                    <Column
                        field="deadline"
                        header="Deadline"
                        :sortable="true"
                    />
                    <Column header="Actions">
                        <template #body="slotProps">
                            <div class="flex gap-1">
                                <Button
                                    as="a"
                                    :href="
                                        ProjectController.edit(
                                            slotProps.data.id,
                                        ).url
                                    "
                                    label="Edit"
                                    size="small"
                                    raised
                                />
                                <Button
                                    v-if="can.delete_project"
                                    label="Delete"
                                    size="small"
                                    severity="secondary"
                                    raised
                                    @click="deleteProject(slotProps.data.id)"
                                />
                            </div>
                        </template>
                    </Column>
                </DataTable>
            </div>
        </div>

        <ConfirmDialog></ConfirmDialog>
    </AppLayout>
</template>
