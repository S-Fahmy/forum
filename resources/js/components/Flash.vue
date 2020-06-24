<template>
  <div class="alert alert-flash" :class="'alert-'+level" role="alert" v-show="show" v-text="body"></div>
</template>

<script>
export default {
  props: ["message"],

  data() {
    return {
      body: "",
      level: "Success",
      show: false
    };
  },
  created() {
    if (this.message) {
      this.flash(JSON.parse(this.message));
    }
     window.events.$on("flash", data => this.flash(data)); //data =  {message, level} json objct in the bootstrap.js function
  },
  methods: {
    flash(data) {
      this.body = data.message;
      this.level = data.level;

      this.show = true;
      this.hide();
    },
    hide() {
      setTimeout(() => {
        this.show = false;
      }, 3000);
    }
  }
};
</script>

<style>
.alert-flash {
  position: fixed;
  right: 25px;
  bottom: 25px;
}
</style>