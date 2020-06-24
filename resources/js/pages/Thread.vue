<script>
import Replies from "../components/Replies.vue";
import subscribeButton from "../components/subscribeButton.vue";

export default {
  //child component
  components: { Replies, subscribeButton },

  props: ["thread"],

  data() {
    return {
      repliesCount: this.thread.replies_count,
      locked: this.thread.locked,
      editing: false,
      form: {
        title: this.thread.title,
        body: this.thread.body
      }
    };
  },

  methods: {
    loadAttachment(){
      console.log("we shouldn't be here");
    },
    toggleLock() {
      //confusing but professional conditional syntax xD
      axios[this.locked ? "delete" : "post"](
        "/forum/locked-threads/" + this.thread.slug
      );
      this.locked = !this.locked;
    },

    update() {
      axios
        .patch(this.thread.path, this.form)
        .then(() => {
          flash({message:"Thread successfully updated"});

          this.editing = false;
        });
    },

    cancel(){
      this.form.title = this.thread.title;
      this.form.body = this.thread.body;
      this.editing = false;
    }
  }
};
</script>