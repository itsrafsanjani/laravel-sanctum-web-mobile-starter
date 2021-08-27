<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        Welcome
                    </div>

                    <div class="card-body">
                        Welcome to our application!
                    </div>

                    <div class="container">
                        <div class="row">
                                <div v-for="i in 10" class="col-md-4 p-2">
                                    <a  class="card">
                                        <img src="https://placekitten.com/200/200/" loading="lazy" class="card-img-top" alt="kitten">
                                        <div class="card-body">
                                            <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card's content.</p>
                                        </div>
                                    </a>
                                </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: 'Welcome'
}
</script>

<style scoped>

</style>
