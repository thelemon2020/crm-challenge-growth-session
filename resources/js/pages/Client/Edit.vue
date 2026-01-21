<template>
    <Form action="/clients" method="post">
        <div>
            <label for="name">Name</label>
            <input type="text" name="name" :value="client.name" />
        </div>

        <div>
            <label for="email">Email</label>
            <input type="email" name="email" :value="client.email"/>
        </div>

        <div>
            <label for="phone">Phone</label>
            <input type="tel" name="phone" :value="client.phone"/>
        </div>

        <div>
            <label for="company">Company</label>
            <input type="text" name="company" :value="client.company"/>
        </div>

        <div>
            <label for="address">Address</label>
            <input type="text" name="address" :value="client.address"/>
        </div>

        <button type="submit">Create User</button>
    </Form>
</template>

<script setup lang="ts">
import { Form } from '@inertiajs/vue3';

defineProps({ client: Object });
</script>
