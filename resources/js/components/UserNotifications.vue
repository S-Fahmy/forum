<template>
  <div>
    <li class="nav-item dropdown" v-if="notifications.length">
      <a id="navbarDropdown" href="#" class="nav-link dropdown-toggle" role="button" data-toggle="dropdown"  aria-haspopup="true" aria-expanded="false" v-pre>
        <i class="fas fa-bell"></i>
      </a>

      <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
        <li v-for="notification in notifications" :key="notification.id">
          <a class="dropdown-item" :href="notification.data.link" @click="markAsRead(notification)">{{notification.data.message}}</a>
        </li>
      </ul>
    </li>
  </div>
</template>

<script>
export default {
  created() {
    axios
      .get("/profiles/" + window.App.user.name + "/notifications")
      .then((response => this.notifications = response.data));
  },

  data() {
    return {
      notifications: false
    };
  },

  methods: {
      markAsRead(notification){

          axios.delete("/profiles/" + window.App.user.name + "/notifications/"+ notification.id);
      }
  }
};
</script>

<style>
</style>