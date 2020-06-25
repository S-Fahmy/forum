<template>
  <div class="reply-card" style="margin-bottom: 30px">
    <div :id="'reply-'+ id" class="card-header reply-header" :class="isBest ? 'bg-success' : ''">
      <div class="row">
        <div class="col-9">
          <img :src="'/forum/'+avatar" :alt="name" width="25" height="auto" class="mr-1" />
          <a :href="'/forum/profiles/'+ name" v-text="name"></a>
          said
          <span v-text="ago"></span> ...
        </div>

        <div class="offset-1 col-2" v-if="signedIn">
          <favorite :reply="data"></favorite>
        </div>
      </div>
    </div>

    <div class="card">
      <div class="card-body">
        <div v-if="editing">
          <form @submit="update">
            <div class="form-group">
              <!-- <textarea class="form-control" v-model="body" required></textarea> -->

              <wysiwyg v-model="body"></wysiwyg>
            </div>
            <button class="btn btn-xs btn-primary">Update</button>

            <button class="btn btn-xs btn-link" type="button" @click="editingCancelled">Cancel</button>
          </form>
        </div>

        <!-- the body attribute in the vue file, which is the same as saying $reply->body-->
        <div v-else>
          <p v-html="body"></p>
        </div>
      </div>
      
      <div class="card-footer reply-footer" v-if="authorize('owns', reply) || authorize('owns', reply.thread)">
        <div class="row">
          <div class="col-10">
            <div v-if="authorize('owns' , reply)">
              <button class="btn-sm btn btn-light" @click="editing = true">
                <font-awesome-icon :icon="['fas', 'edit']" />
              </button>

              <button class="btn-sm btn btn-light" @click="destroy">
                <font-awesome-icon :icon="['fas', 'trash']" />
              </button>
            </div>
          </div>

          <div class="col-2">
            <button
              class="btn-sm btn btn-light"
              @click="changeBestReply"
              v-show="! isBest"
              v-if="authorize('owns', reply.thread)"
            >
              <font-awesome-icon :icon="['fas', 'check']" />
              <span class="bestReply">Best Reply</span>
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import Favorite from "./Favorite.vue";
//var moment = require('moment'); // require
import moment from "moment";

export default {
  props: ["data"],
  components: { Favorite },

  data() {
    return {
      editing: false,
      reply: this.data,
      //get the body from the data props i added in the reply.blade
      //its a json file that has all the reply data
      body: this.data.body, //this is for setting the default text area value
      id: this.data.id,
      name: this.data.owner.name,
      avatar: this.data.owner.avatar_path,
      isBest: this.data.isBest
    };
  },

  computed: {
    ago() {
      return moment(this.data.created_at).fromNow();
    }
  },

  created() {
    //when another reply component get set as best reply
    window.events.$on("best-reply-selected", id => {
      this.isBest = id === this.id; //true or false
    });
  },

  methods: {
    update() {
      //use the defined edit routing patch link and send it the updated
      axios
        .patch("/forum/replies/" + this.data.id, {
          body: this.body
        })
        .catch(error => {
          // console.log(error.response);
          flash(error.response.data, "danger");
        });

      this.editing = false;
      flash("Post edited.", "success");
    },

    editingCancelled() {
      this.editing = false;
      this.body = this.data.body;
    },

    changeBestReply() {
      axios.post("/forum/replies/" + this.data.id + "/best");

      // this.isBest = true;
      //when a reply component gets set as best reply, it emits an event that tells the all reply components about it
      window.events.$emit("best-reply-selected", this.data.id);
    },

    destroy() {
      axios.delete("/forum/replies/" + this.data.id);

      //we delete the reply we get the response from the server then we apply a
      //fade out effect to the deleted element using jquery then we emit to parent reply
      $(this.$el).fadeOut(300, () => {
        this.$emit("deleted", this.data.id);
      });
    }
  }
};
</script>

<style  scoped>
.reply-header {
  background-color: white;
}
/* .card-header:first-child {
    border-radius: 0px;
} */
.card-body {
  border-radius: 0px;
}

.reply-footer {
  max-height: 27.5px;
  padding: 0px;
  padding-left: 1.23rem;
  padding-right: 1.23rem;
  overflow: hidden;
}

.bestReply {
  font-size: 0.8em;
}

.card,
.card-header,
.card-body {
  border-radius: 0px !important;
}
</style>