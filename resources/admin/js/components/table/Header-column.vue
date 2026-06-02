<template>
  <th @click="sort(field, order)">{{ title }} 
    <!-- <i class="fe fa-fw fa-sort" v-if="currentField !== field" aria-hidden="true"></i> -->
    <i class="fe fe-chevrons-up" v-if="currentField === field && order === 'ASC'" aria-hidden="true"></i>
    <i class="fe fe-chevrons-down" v-if="currentField === field && order === 'DESC'" aria-hidden="true"></i>
  </th>
</template>

<script>
export default {
  props: ['field', 'title', 'currentField', 'order'],
  methods: {
    sort: function (field, order) {
      // order = (order === 'ASC') ? 'DESC' : 'ASC'
      this.$emit('sort', field, (order === 'ASC') ? 'DESC' : 'ASC')
    }
  }
}
</script>

<style lang="scss">
th {
  cursor: pointer;
}
</style>