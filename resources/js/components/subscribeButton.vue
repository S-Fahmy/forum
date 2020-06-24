<template>
  <button :class="classes" @click="subscribe" v-text="buttonText"></button>
</template>

<script>
export default {
  props: ["active"],

  computed: {
    classes() {
      return ["btn btn-sm subButton", this.active ?  "btn-primary" :"btn-dark"];
    },

    buttonText() {
      return this.active ? "Unsubscribe" : "Subscribe";
    }
  },

  methods: {
    subscribe() {
      //we need an if condition, if active is true submit delete request else submit post
      this.active
        ? axios.delete(location.pathname + "/subscriptions").then(this.active = false)
        : axios.post(location.pathname + "/subscriptions").then(this.active = true);

      //   flash("subscribed!");
    }
  }
};
</script>

<style scoped>
.subButton{
  font-size: .8em;
  margin-right: 10px;
}
</style>