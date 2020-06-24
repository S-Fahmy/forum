<template>
  <ul class="pagination" v-if="shouldPaginate">
    <li class="page-item" v-show="prevUrl">
      <a class="page-link" href="#" aria-label="Previous" rel="prev" @click.prevent="currentPage--">
        <span aria-hidden="true">&laquo; Previous</span>
        <span class="sr-only">Previous</span>
      </a>
    </li>
    <div v-for="index in totalPages" :key="index">
      <li class="page-item" :class="index == currentPage ? 'active' : ''">
        <a class="page-link" href="#" @click.prevent="currentPage = index">{{index}}</a>
      </li>
    </div>
    <!-- <li class="page-item">
      <a class="page-link" href="#">1</a>
    </li>-->

    <li class="page-item" v-show="nextUrl">
      <a class="page-link" href="#" aria-label="Next" rel="next" @click.prevent="currentPage++">
        <span aria-hidden="true">Next &raquo;</span>
        <span class="sr-only">Next</span>
      </a>
    </li>
  </ul>
</template>

<script>
export default {
  //dataSet contains paginations infos
  props: ["dataSet"],

  data() {
    return {
      totalPages: 0,
      currentPage: 1, // current page url
      prevUrl: false, // prev url button
      nextUrl: false //  next url button
    };
  },

  watch: {
    //keep en eye for whenever the dataSet on the replies component gets changes we update the pages urls here
    dataSet() {
      this.totalPages = this.dataSet.last_page;
      this.currentPage = this.dataSet.current_page;

      //we get the linka from the dataset this way so we know if there is any prev or next pages and disable/enable button
      this.prevUrl = this.dataSet.prev_page_url;
      this.nextUrl = this.dataSet.next_page_url;
    },

    //keep an eye for when a user clicks next or prev and changes the currentPage value
    currentPage() {
      // when it changes we will emit an even for the parent component then update the url in the browser
      this.broadcast();
      this.updateUrl();
    }
  },

  computed: {
    shouldPaginate() {
      return !!this.prevUrl || !!this.nextUrl;
    },
    classes(index) {
      //return page-item anyway then the styling
      return ["page-item", index == this.currentPage ? "active" : "xx"];
    }
  },

  methods: {
    broadcast() {
      this.$emit("changed", this.currentPage); //now its the responsibility of the replies component to display new replies
    },

    updateUrl() {
      //using javascript history api
      history.pushState(null, null, "?page=" + this.currentPage);
    }
  }
};
</script>