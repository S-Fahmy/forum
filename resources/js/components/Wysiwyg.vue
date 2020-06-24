<template>
  <div>
    <input id="trix" type="hidden" :name="name" :value="value" />
    <trix-editor
      ref="trix"
      input="trix"
      :placeholder="placeholder"
      @keypress="isItAtSign($event)"
      @trix-attachment-add="uploadFileAttachment($event)"
      @trix-attachment-remove="deleteUploadedAttachment($event)"
    ></trix-editor>
  </div>
</template>

<script>
import Trix from "trix";
import Tribute from "tributejs";
var tribute;

export default {
  props: ["name", "value", "placeholder", "shouldClear", "attributes"],
  data() {
    return {
      usersMentionsList: [],
      attachmentAttributes: [], //gets sent to the parent component
      userId: window.App.user.id,
      bodyGotCleared: false //so it dont call the attach remove event
    };
  },

  mounted() {
    // the code used to sync the trix editor with the form.body when edited,
    //make an event listener for when the trix editor gets changed, then change the trix input value to the new one
    this.$refs.trix.addEventListener("trix-change", e => {
      this.$emit("input", e.target.innerHTML);
    });

    this.$watch("shouldClear", () => {
      // i set the booleanb as true then clear the body, so trix triggers the remove event, remove event works on false, then i resets it
      this.bodyGotCleared = true;

      this.$refs.trix.value = "";

      this.bodyGotCleared = false;
    });
  },

  methods: {
    uploadFileAttachment(e) {
      var attachment = e.attachment.attachment;
      if (attachment.file == undefined) {
        return;
      }
      this.uploadFile(attachment.file, setProgress, setAttributes);

      function setProgress(progress) {
        attachment.setUploadProgress(progress);
      }

      function setAttributes(attributes) {
        attachment.setAttributes(attributes);
      }

      return;
    },

    uploadFile(file, progressCallback, successCallback) {
      if (file == undefined) {
        return;
      }

      this.$emit("uploadingAttachment"); //to block the post button

      console.log("uploading!");
      let formData = new FormData();
      formData.append("attached", file);
      axios
        .post("/forum/api/users/" + this.userId + "/attachments", formData, {
          headers: {
            "Content-Type": "multipart/form-data"
          },
          onUploadProgress: progressEvent => {
            const totalLength = progressEvent.lengthComputable
              ? progressEvent.total
              : progressEvent.target.getResponseHeader("content-length") ||
                progressEvent.target.getResponseHeader(
                  "x-decompressed-content-length"
                );
            console.log("onUploadProgress", totalLength);
            if (totalLength !== null) {
              const progressData = Math.round(
                (progressEvent.loaded * 100) / totalLength
              );
              progressCallback(progressData);
            }
          }
        })
        .then(response => {
          progressCallback(100);

          let url = response.data.url;
          var attributes = {
            url: 'http://ist-industries.com/forum/' + url,
            href:'http://ist-industries.com/forum/' + url + "?content-disposition=attachment"
          };
          console.log(attributes.href);
          successCallback(attributes);

          this.$emit(
            "haveAttachment",
            (this.attachmentAttributes = response.data)
          );
        })

        .catch(function() {
          console.log("FAILURE!!");
        });
    },

    deleteUploadedAttachment(e) {
      //  this gets called even when the value of the attachment gets emptied, so lame

      if (!this.bodyGotCleared) {
        console.log("dum trix");
        axios.delete("/forum/api/attachments/" + this.attachmentAttributes.id);
      }
      // else {
      //   console.log("body got cleared true: " + this.bodyGotCleared);
      //   this.bodyGotCleared = false; //just manually resetting it
      //   console.log(this.bodyGotCleared);
      // }
    },

    isItAtSign(event) {
      if (this.usersMentionsList.length == 0 && event.key == "@") {
        //checking only for the first @ to attach the tribute object to the element then its tribute library job after that
        this.loadMentionsList();
      }
    },
    loadMentionsList() {
      axios.get("/forum/api/users").then(this.tributeAutoComplete); // getting all the user names for now.
    },

    tributeAutoComplete(response) {
      this.usersMentionsList = response.data;

      tribute = new Tribute({
        values: this.usersMentionsList,

        lookup: "name",
        fillAttr: "name"
      });

      tribute.attach(this.$refs.trix);
    }
  }
};
</script>
<style scoped>
trix-editor .trix-button {
  display: none !important;
  color: yellow !important;
}
</style>