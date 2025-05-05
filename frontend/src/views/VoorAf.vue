<template>
    {{slug}}
</template>
<script>
export default{
    name: "SlugView",
    data() {
        return {
            slug: this.$route.params.slug,
        };
    },
    mounted(){

    },
    methods: {
        // Add any methods you need here
    },
}
</script>