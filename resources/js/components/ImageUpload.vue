<template>
  <div>
    <label
      class="pfplabel btn btn-sm btn-light"
      onclick="document.getElementById('getFile').click()"
    >
      <font-awesome-icon :icon="['fas', 'pen']" />
    </label>
    <input type="file" id="getFile" accept="image/*" @change="onChange" style="display: none;" />
  </div>
</template>

<script>
export default {
  methods: {
    onChange(event) {
      if (!event.target.files.length) return;
      let file = event.target.files[0];
      let reader = new FileReader();
      reader.readAsDataURL(file);
      reader.onload = event => {
        let src = event.target.result;
        this.$emit("loaded", { src, file });
      };
    }
  }
};
</script>
<style scoped>
.pfplabel {
  border: 1px solid black;
  border-radius: 100%;
  margin-top: -80px;
}
</style>