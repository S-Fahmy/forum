<template>
  <div>
    <div class="card">
      <div class="card-header"></div>
      <div class="card-body">
        <img :src="avatar" class="avatar" width="80px" height="auto" />

        <form v-if="canUpdate" method="POST" enctype="multipart/form-data">
          <image-upload name="avatar" class=" mr-1" @loaded="onLoad"></image-upload>
        </form>

        <h5>{{ name }}</h5>
        <p>{{name}} has # threads and # replies.</p>
 
      </div>
    </div>
  </div>
</template>

<script>
import ImageUpload from "./ImageUpload.vue";
export default {
  components: { ImageUpload },
  props: ["userProfile"],

  data() {
    return {
      name: this.userProfile.name,
      avatar: "/forum/" + this.userProfile.avatar_path
    };
  },

  computed: {
    canUpdate() {
      return this.authorize(user => user.id === this.userProfile.id);
    }
  },

  methods: {
    onLoad(avatar) {
      //the load event sends a {file, src} object
      this.persist(avatar.file); //the actual file
      this.avatar = avatar.src; //the image data uri
    },

    persist(avatar) {
      let data = new FormData(); //this is a key/values object that simulate a DOM form
      data.append("avatar", avatar);
      axios
        .post("/forum/api/users/" + this.userProfile.name + "/avatar", data)
        .then(() => flash("Avatar uploaded!", "success"));
    }
  }
};
</script>

<style scoped>
.card-header{
  min-height: 70px;
  background-color: #33a8ff;
}
.avatar{
  margin-top: -113px;
}
</style>