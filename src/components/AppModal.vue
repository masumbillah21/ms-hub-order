<script setup>
    import { ref, defineProps, defineEmits } from 'vue';

    const props = defineProps({
        isOpen: Boolean,
        title: {
            type: String,
            default: 'Modal Title'
        }
    });

    const emit = defineEmits();

    const closeModal = () => {
        emit('update:isOpen', false);
    };
</script>

<template>
    <div v-if="isOpen" class="modal-overlay">
      <div class="modal">
        <div class="modal-content">
          <div class="modal-header">
            <h2>{{ title }}</h2>
            <button @click.stop="closeModal">&times;</button>
          </div>
          <div class="modal-body">
            <slot></slot>
          </div>
        </div>
      </div>
    </div>
  </template>
  
  
  <style scoped>
  .modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 999;
  }
  
  .modal {
    min-width: 350px;
    background-color: white;
    border-radius: 8px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
    z-index: 9999;
  }
  
  .modal-content {
    padding: 20px;
  }
  
  .modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 10px;
  }
  
  .modal-header h2 {
    margin: 0;
  }
  
  .modal-header button {
    background: none;
    border: none;
    cursor: pointer;
    font-size: 1.5rem;
  }
  </style>
  