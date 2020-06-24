<template>
  <div class="container">
    <ais-instant-search :search-client="searchClient" index-name="threads" :routing="routing">
      <div class="row">
        <div class="col-8">
          <ais-hits>
            <div slot="item" slot-scope="{ item }">
              <!-- <h2>{{ item.title }}</h2> -->

              <div class="hit-names">
                <div class="card">
                  <div class="card-body">
                    <a :href="item.path">
                      <ais-highlight attribute="title" :hit="item"></ais-highlight>
                    </a>
                  </div>
                </div>
              </div>
            </div>
          </ais-hits>
        </div>

        <div class="col-4">
          <div class="card">
            <div class="card-header">
              <h4>Search Threads</h4>
            </div>
            <div class="card-body">
              <ais-search-box autofocus show-loading-indicator placeholder="Search..." />
            </div>
          </div>
          <div class="card">
            <div class="card-header">
              <h4>filter Threads</h4>
            </div>
            <div class="card-body">
              <ais-refinement-list attribute="channel.name" />
            </div>
          </div>
          <div class="card">
            <div class="card-header">Trending threads</div>

            <div class="card-body">
              <ul id="example-1" class="list-group">
                <li v-for="item in trendingThreads" :key="item.path" class="list-group-item">
                  <a href="item.path">{{item.title}}</a>
                </li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </ais-instant-search>
  </div>
</template>

<script>
import algoliasearch from "algoliasearch/lite";
import { history as historyRouter } from "instantsearch.js/es/lib/routers";
import { simple as simpleStateMapping } from "instantsearch.js/es/lib/stateMappings";
// import "instantsearch.css/themes/algolia-min.css";

export default {
  props: ["trending"],
  data() {
    return {
      trendingThreads: this.trending,

      searchClient: algoliasearch(
        "468SQC7QIJ",
        "08f18aa5447925aafb73fedad030e7e2"
      ),

      routing: {
        router: historyRouter(),
        stateMapping: simpleStateMapping()
      }
    };
  }
};
</script>

<style>
.ais-Highlight-highlighted {
  background: cyan;
  font-style: normal;
}

.hit-names {
  margin-bottom: 2px;
}
</style>
