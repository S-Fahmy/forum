<template>
  <button :class="classes" @click="toggle">
    <i class="fas fa-heart"></i>
    <span v-text="favoritesCount"></span>
  </button>
</template>


<script>
export default {
  props: ["reply"],

  data() {
    return {
      //favoritesCount is a custom value appended to the elequant array in the reply model class
      favoritesCount: this.reply.favoritesCount,
      isFavorited: this.reply.isFavorited
    };
  },
  computed: {
    classes() {
      //return btn anyway then the styling
      return ["btn", this.isFavorited ? "btn-primary" : "btn-light"];
    }
  },
  methods: {
    //toggle favrite

    toggle() {
      //if its favorited unfav.....

      this.isFavorited ? this.unfavorite() : this.favorite();
    },

    favorite(){
      axios.post("/forum/replies/" + this.reply.id + "/favorites");
      this.favoritesCount++;
      this.isFavorited = true;
    },

    unfavorite() {
      axios.delete("/forum/replies/" + this.reply.id + "/favorites");
      this.favoritesCount--;
      this.isFavorited = false;
    }
  }
};
</script>