<template>
  <AdminTemplate :tabs="tabs">
    <submitted-requests :requests="requests" />
  </AdminTemplate>
</template>

<script>
import SubmittedRequests from "../SubmittedRequests.vue";
import AdminTemplate from "@/Pages/Srp/Admin/AdminTemplate.vue";

export default {
    name: "Review",
    components: {AdminTemplate, SubmittedRequests},
    props: {
        requests: {
            type: Object,
            required: true
        },
        tabs: {
            type: Array,
            required: true
        }
    }
}
</script>

<style scoped>

</style>