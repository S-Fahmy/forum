<template>
  <!-- @if (auth()->check()) -->
  <!-- <form method="POST" action="{{ $thread->path() . '/replies' }}"> -->
  <!-- {{ csrf_field() }} -->
  <div>
    <div v-if="signedIn">
      <div class="form-group">
        <wysiwyg
          v-model="body"
          placeholder="Type a new reply"
          :shouldClear="completed"
          @uploadingAttachment="attachmentIsUploading = true"
          @haveAttachment="setAttachment"
        ></wysiwyg>

        <!-- @attachmentAdded="uploadFileAttachment" -->
      </div>
      <button
        type="submit"
        class="btn btn-dark"
        :disabled="attachmentIsUploading"
        @click="addReply"
      >Post</button>
    </div>

    <div v-else>
      <p class="text-center">
        Please
        <a href="/login">sign in</a> to participate in this
        discussion.
      </p>
    </div>
  </div>
</template>


<script>
import Tribute from "tributejs";
var tribute;
export default {
  props: ["endpoint"],

  data() {
    return {
      body: "",
      completed: false,
      attachmentIsUploading: false,
      attachedFile: null,
      replyForm: new FormData()
    };
  },

  methods: {
    setAttachment(file) {
      this.attachedFile = file; //sent from child component
      this.attachmentIsUploading = false;
    },

    addReply() {
      //append the body to the replyForm, i took the form object approach because it can contain attachments
      //make a post request to the reply end point
      //when we get back the data we reset the body, display flash message and add the new reply to replies
      this.completed = false; //reset

      axios
        .post(this.endpoint, {
          body: this.body,
          includesAttachments: this.attachedFile
        })
        .catch(error => {
          flash(error.response.data.errors.body[0], "danger");
        })
        .then(({ data }) => {
          this.body = "";
          this.completed = true; // we pass this to the wysiwyg component to reset itself and it has a watcher ready for it
          this.attachedFile = null; //reset
          flash("the reply has been posted", "success");

          //emit an even for the replies component
          this.$emit("created", data);
        });
    }
  }
};
</script>

<style scoped>
.trix-editor .trix-button--remove button {
  display: none !important;
}
</style>