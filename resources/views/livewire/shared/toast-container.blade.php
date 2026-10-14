<div class="toast-container" x-data>
    <template x-for="item in $store.toasts.items" :key="item.id">
        <div class="toast" :class="item.type">
            <div class="toast-content">
                <div class="toast-title" x-text="item.title"></div>
                <div class="toast-message" x-show="item.message" x-text="item.message"></div>
                <button type="button" class="toast-undo" x-show="item.undoAction" @click="$dispatch(item.undoAction); $store.toasts.remove(item.id)">
                    Deshacer
                </button>
            </div>
            <button type="button" class="toast-close" @click="$store.toasts.remove(item.id)">×</button>
            <div class="toast-progress" :style="`animation-duration: ${item.duration}ms`"></div>
        </div>
    </template>
</div>
