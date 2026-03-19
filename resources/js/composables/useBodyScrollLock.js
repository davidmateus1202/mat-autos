import { onBeforeUnmount, unref, watch } from 'vue'

let activeLocks = 0
let previousBodyOverflow = ''
let previousHtmlOverflow = ''
let previousBodyTouchAction = ''
let previousBodyPaddingRight = ''

function lockScroll() {
    if (typeof document === 'undefined' || typeof window === 'undefined') {
        return
    }

    const body = document.body
    const html = document.documentElement

    if (activeLocks === 0) {
        previousBodyOverflow = body.style.overflow
        previousHtmlOverflow = html.style.overflow
        previousBodyTouchAction = body.style.touchAction
        previousBodyPaddingRight = body.style.paddingRight

        const scrollbarGap = window.innerWidth - html.clientWidth

        body.style.overflow = 'hidden'
        body.style.touchAction = 'none'
        html.style.overflow = 'hidden'

        if (scrollbarGap > 0) {
            body.style.paddingRight = `${scrollbarGap}px`
        }

        body.dataset.scrollLocked = 'true'
        html.dataset.scrollLocked = 'true'
    }

    activeLocks += 1
}

function unlockScroll() {
    if (typeof document === 'undefined' || activeLocks === 0) {
        return
    }

    activeLocks -= 1

    if (activeLocks > 0) {
        return
    }

    const body = document.body
    const html = document.documentElement

    body.style.overflow = previousBodyOverflow
    body.style.touchAction = previousBodyTouchAction
    body.style.paddingRight = previousBodyPaddingRight
    html.style.overflow = previousHtmlOverflow

    delete body.dataset.scrollLocked
    delete html.dataset.scrollLocked
}

export function useBodyScrollLock(source) {
    let isLocked = false

    const syncLockState = (value) => {
        const shouldLock = Boolean(unref(value))

        if (shouldLock === isLocked) {
            return
        }

        if (shouldLock) {
            lockScroll()
            isLocked = true
            return
        }

        unlockScroll()
        isLocked = false
    }

    const stopWatching = watch(
        () => Boolean(unref(source)),
        (value) => {
            syncLockState(value)
        },
        { immediate: true }
    )

    onBeforeUnmount(() => {
        stopWatching()

        if (isLocked) {
            unlockScroll()
        }
    })
}
