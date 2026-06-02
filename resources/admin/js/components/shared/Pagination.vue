<template>
  <nav>
    <ul class="pagination">
      <li v-if="pagination.current_page > 1" class="page-item">
        <a href="javascript:void(0)" aria-label="Previous" class="page-link" v-on:click.prevent="changePage(pagination.current_page - 1)">
          <span aria-hidden="true">«</span>
        </a>
      </li>
      <li v-for="(page, index) in pagesNumber" :key="index" class="page-item" :class="{'active': page == pagination.current_page}">
        <a href="javascript:void(0)" class="page-link" v-on:click.prevent="changePage(page)">{{ page }}</a>
      </li>
      <li v-if="pagination.current_page < pagination.last_page" class="page-item">
        <a href="javascript:void(0)" aria-label="Next" class="page-link" v-on:click.prevent="changePage(pagination.current_page + 1)">
          <span aria-hidden="true">»</span>
        </a>
      </li>
    </ul>
  </nav>
</template>

<script>
export default {
  props: {
    pagination: {
      type: Object,
      required: true
    },
    offset: {
      type: Number,
      default: 4
    }
  },
  computed: {
    pagesNumber () {
      if (!this.pagination.to) {
        return []
      }
      let from = this.pagination.current_page - this.offset
      if (from < 1) {
        from = 1
      }
      let to = from + (this.offset * 2)
      if (to >= this.pagination.last_page) {
        to = this.pagination.last_page
      }
      let pagesArray = []
      for (let page = from; page <= to; page++) {
        pagesArray.push(page)
      }
      return pagesArray
    }
  },
  methods: {
    changePage (page) {
      this.pagination.current_page = page
      this.$emit('paginate')
    }
  }
}
</script>
