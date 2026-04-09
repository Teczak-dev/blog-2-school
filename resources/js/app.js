import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

// Comments functionality
document.addEventListener('DOMContentLoaded', function () {
    const refreshCommentsSection = async function () {
        const currentCommentsContainer = document.getElementById('comments-container');
        if (!currentCommentsContainer) {
            return;
        }

        const url = new URL(window.location.href);
        const sortSelect = document.getElementById('comment-sort');
        if (sortSelect?.value) {
            url.searchParams.set('sort', sortSelect.value);
        }

        const response = await fetch(url.toString(), {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
            },
        });

        if (!response.ok) {
            throw new Error('Failed to refresh comments section');
        }

        const html = await response.text();
        const parser = new DOMParser();
        const documentFromResponse = parser.parseFromString(html, 'text/html');
        const newCommentsContainer = documentFromResponse.getElementById('comments-container');
        const newCountValue = documentFromResponse.getElementById('comments-count-value');

        if (newCommentsContainer) {
            currentCommentsContainer.innerHTML = newCommentsContainer.innerHTML;
        }

        const currentCountValue = document.getElementById('comments-count-value');
        if (newCountValue && currentCountValue) {
            currentCountValue.textContent = newCountValue.textContent;
        }
    };

    const showSubmitLoadingState = function (submitButton) {
        if (!submitButton) {
            return;
        }

        submitButton.dataset.originalContent = submitButton.innerHTML;
        submitButton.innerHTML = `
            <svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
            </svg>
            Wysyłanie...
        `;
        submitButton.disabled = true;
    };

    const resetSubmitButton = function (submitButton) {
        if (!submitButton) {
            return;
        }

        submitButton.innerHTML = submitButton.dataset.originalContent || submitButton.innerHTML;
        submitButton.disabled = false;
    };

    const showSuccessMessage = function (form, message) {
        const hostNode = form.parentNode;
        if (!hostNode) {
            return;
        }

        const existingMessage = hostNode.querySelector('.success-message');
        if (existingMessage) {
            existingMessage.remove();
        }

        const successMessage = document.createElement('div');
        successMessage.className = 'success-message mb-4 bg-green-50 dark:bg-green-900/20 border-l-4 border-green-400 dark:border-green-500 p-4';
        successMessage.innerHTML = `
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400 dark:text-green-300" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-green-700 dark:text-green-300">${message}</p>
                </div>
            </div>
        `;

        hostNode.insertBefore(successMessage, form);

        setTimeout(() => {
            successMessage.remove();
        }, 5000);
    };

    document.addEventListener('click', async function (event) {
        const loadMoreButton = event.target.closest('#load-more-comments');
        if (!loadMoreButton) {
            return;
        }

        event.preventDefault();

        const postId = loadMoreButton.dataset.postId;
        const currentOffset = parseInt(loadMoreButton.dataset.offset, 10);
        const commentsList = document.getElementById('comments-list');
        if (!postId || Number.isNaN(currentOffset) || !commentsList) {
            return;
        }

        const originalButtonContent = loadMoreButton.innerHTML;
        loadMoreButton.innerHTML = `
            <svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
            </svg>
            Ładowanie...
        `;
        loadMoreButton.disabled = true;

        try {
            const response = await fetch(`/posts/${postId}/comments/load-more?offset=${currentOffset}`);
            if (!response.ok) {
                throw new Error('Error loading comments');
            }

            const data = await response.json();
            data.comments.forEach(comment => {
                const commentHtml = `
                    <div class="flex gap-4 mb-6">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-gradient-to-br from-blue-400 to-blue-600 rounded-full flex items-center justify-center text-white font-semibold">
                                ${comment.author_name.substring(0, 2).toUpperCase()}
                            </div>
                        </div>
                        <div class="flex-1">
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                <div class="flex items-center justify-between mb-2">
                                    <div class="flex items-center gap-2">
                                        <h4 class="font-semibold text-gray-900 dark:text-gray-100">${comment.author_name}</h4>
                                        ${comment.is_from_logged_user ? '<span class="px-2 py-1 bg-green-100 dark:bg-green-800 text-green-700 dark:text-green-300 text-xs rounded-full">Użytkownik</span>' : ''}
                                    </div>
                                    <span class="text-sm text-gray-500 dark:text-gray-400">${comment.created_at}</span>
                                </div>
                                <div class="text-gray-700 dark:text-gray-300 leading-relaxed whitespace-pre-line">${comment.content}</div>
                            </div>
                        </div>
                    </div>
                `;
                commentsList.insertAdjacentHTML('beforeend', commentHtml);
            });

            if (data.hasMore) {
                loadMoreButton.dataset.offset = String(currentOffset + data.comments.length);
                loadMoreButton.innerHTML = originalButtonContent;
                loadMoreButton.disabled = false;
            } else {
                loadMoreButton.remove();
            }
        } catch (error) {
            console.error('Error loading comments:', error);
            loadMoreButton.innerHTML = `
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
                Błąd - spróbuj ponownie
            `;
            loadMoreButton.disabled = false;
        }
    });

    document.addEventListener('submit', async function (event) {
        const form = event.target.closest('form[data-comment-form="true"]');
        if (!form) {
            return;
        }

        event.preventDefault();

        const submitButton = form.querySelector('button[type="submit"]');
        showSubmitLoadingState(submitButton);

        try {
            const response = await fetch(form.action, {
                method: (form.getAttribute('method') || 'POST').toUpperCase(),
                body: new FormData(form),
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
            });

            if (!response.ok) {
                throw new Error('Error submitting comment form');
            }

            const data = await response.json();
            if (!data?.success) {
                throw new Error('Comment form response was not successful');
            }

            form.reset();
            const replyFormContainer = form.closest('.reply-form-container');
            if (replyFormContainer) {
                replyFormContainer.classList.add('hidden');
            }

            showSuccessMessage(form, data.message);
            await refreshCommentsSection();
        } catch (error) {
            console.error('Error submitting comment:', error);
            window.location.reload();
        } finally {
            resetSubmitButton(submitButton);
        }
    });
});
