<template>
    <div class="ml-4 text-white lg:text-gray-700 mr-4">
        <Link :href="route('language', 'ar')">{{__('ar')}}</Link>
            <br>
        <Link :href="route('language', 'en')">{{__('en')}}</Link>
    </div>
</template>

<script>
export default {
    name: "LanguageSelector",
    computed: {
        selectable_locale() {
            if(this.$page.locale == 'ar') {
                return 'en';
            }
            return 'ar'
        }
    },
}
</script>

<style scoped>

</style>
