<template>
  <div class="modal-mask">
    <div class="modal-wrapper">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          
          <div class="modal-header">
            <h5 class="modal-title">
              <slot name="header"></slot>
            </h5>
            <button type="button" 
              @click="$emit('close')" 
              class="close" 
              data-dismiss="modal" 
              aria-label="Close">
            </button>
          </div>
          
          <div class="modal-body">
            <slot></slot>
          </div>

          <div class="modal-footer">
            <slot name="footer"></slot>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  mounted() {
    const el = document.body;
    el.classList.add('modal-disable-body');
  },

  destroyed() {
    const el = document.body;
    el.classList.remove('modal-disable-body');
  }
}
</script>

<style lang="scss">
.modal-mask {
  position: fixed;
  z-index: 10;
  top: 0;
  left: 0;
  width: 100%;
  height: 100vh;
  background-color: rgba(0, 0, 0, .5);
  display: table;
  transition: opacity .3s ease;
}

.modal-wrapper {
  display: table-cell;
  vertical-align: middle;
  height: 100vh;
}

.modal-body {
  overflow: auto;
  max-height: calc(100vh - 250px);
}

.modal-footer {
  display: flex;
  flex-wrap: wrap;
}

.modal-footer button {
  margin-bottom: 5px;
}
</style>
