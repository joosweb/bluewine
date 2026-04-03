<template>
	<div class="ks-column ks-page">
			<div class="ks-page-content">
					<div class="ks-page-content-body ks-error-page">
							<div class="ks-error-code">404</div>
							<div class="ks-error-description">Sorry, but this page doesn't exists</div>
							<a href="/" class="btn btn-primary ks-light">Go to the main page</a>
					</div>
			</div>
	</div>
</template>

<script>
    export default {
        mounted() {
            console.log('Component mounted.')
        }
    }
</script>
