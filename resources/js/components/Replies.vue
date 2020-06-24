
<template>
  <div>
    <div v-for="(reply, index) in items" :key="reply.id">
      <!-- the child component Reply will send a deleted even when the user deleted a reply -->

      <reply :data="reply" @deleted="remove(index)"></reply>
    </div>
    <paginator :dataSet="dataSet" @changed="fetch"></paginator>

    <p v-if="$parent.locked">this thread has been locked!</p>

    <new-reply :endpoint="endpoint" @created="add" v-if="! $parent.locked || authorize('isAdmin')"></new-reply>
  </div>
</template>

<script>
//importing the Reply.vue component to be used in this templat
import Reply from "./Reply.vue";
import NewReply from "./NewReply.vue";

export default {
  //child component
  components: { Reply, NewReply },

  props: [],

  data() {
    return {
      //the full dataSet returned with the paginations data
      dataSet: false,
      // this is going to get all the replies data collection from the axios call
      //array of replies objects
      items: [],
      endpoint: location.pathname + "/replies"
    };
  },

  //once this component gets summoned we fetch the replies collection
  created() {
    this.fetch();
  },

  methods: {
    //we use the get replies route then we call a refresh function that fills the items array and the dataSet
    fetch(page) {
      axios.get(this.url(page)).then(this.refresh);
    },

    url(page) {
      //if this a fresh refresh then the page paginator variable will be null so we manually check if the user enteree a url with a page query
      //then we extract it with regex if not we add the default 1
      if (!page) {
        let query = location.search.match(/page=(\d+)/);

        page = query ? query[1] : 1;
      }
      return location.pathname + "/replies?page=" + page; // /threads/{channel}/{thread}/replies route
    },

    //this function fills the dataSet and items with the data the server sent as a response after the axios ajax request
    refresh(response) {
      //console.log(response);

      // the dataSet will hold an object the has all the paginations info and the array of replies data that wee want to display
      this.dataSet = response.data;
      this.items = this.dataSet.data;

      //scroll to the top of the page
      window.scrollTo(0, 0);
    },

    add(reply) {
      this.items.push(reply);

      this.$emit("added");
    },
    remove(index) {
      this.items.splice(index, 1);
      //emit a removed event for the parent thread component, based on this it change the number of replies count
      this.$emit("removed");

      flash("your reply has been deleted.", "success");
    }
  }
};
</script>