# Blog

A blog application built with Laravel 12, Filament v5, and Tailwind CSS v4. Features post management with a Filament admin panel.

## Tech Stack

- **PHP** 8.4
- **Laravel** 12
- **Filament** 5
- **Tailwind CSS** 4
- **SQLite** (default)
- **Pest** 4 (testing)

## Project Features

### Authentication and account

- Registration, login, logout
- Password reset flow (forgot/reset password)
- Email verification
- User profile edit/update/delete
- Notification preferences in profile (email notifications, post/message/friend request toggles)
- Theme preference update (light/dark/system)

### Posts

- Public posts list (`/`, `/posts`)
- Post details page (`/posts/{id}`)
- Authenticated post creation and editing
- Post categories, read time, optional photo upload
- Related posts section on post details page
- Social sharing actions on post page (Facebook, X/Twitter, LinkedIn, WhatsApp, Messenger, copy link)
- Instagram sharing fallback (opens Instagram and copies post URL)

### Comments

- Add comments under posts (guest and authenticated users)
- Nested replies to top-level comments
- Guest comments/replies require moderation, authenticated users are auto-approved
- Load more comments
- Like/dislike voting on comments (authenticated users)
- Sorting comments (newest/oldest/most liked)
- AJAX comment/reply submission without manual page refresh
- Automatic comments section refresh after posting a comment/reply

### Social features

- User discovery page (`/users`)
- User profile page with followers/following/friends views
- Follow/unfollow users
- Send/accept/reject/cancel/remove friendship requests
- Friend requests and friends management pages

### Messaging

- Conversations list and conversation detail view
- Start private conversation
- Send, edit, delete messages
- Mark conversation/message as read
- Unread messages counter
- Leave conversation

### Notifications

- Event-driven notifications for new posts, comments, and messages
- Email templates for account verification and activity notifications

### UI/UX

- Dark mode across application pages and components
- Responsive layouts for posts, users, friends, profile, and conversations

### Admin/maintenance

- Filament admin panel integration
- Automated tests with Pest

## Installation

```bash
git clone https://github.com/Teczak-dev/blog.git
cd blog
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan storage:link
php artisan boost:install
npm run build
```

## Running the Application

```bash
composer run dev
```

This starts the Laravel dev server, queue worker, log viewer, and Vite simultaneously.

## Troubleshooting (sharing / comments)

If social sharing does not work on production, verify:

- the app uses a **public HTTPS URL** (not localhost/private IP),
- popups are not blocked in browser,
- `APP_URL` in `.env` points to the public domain,
- CSP/firewall/proxy rules do not block `facebook.com`, `twitter.com`, `x.com`, `linkedin.com`, `wa.me`.

If something still fails, collect and share:

1. Full page URL where the issue occurs.
2. Browser + device name.
3. Exact button clicked (e.g. Facebook / X / Messenger).
4. Browser console error (DevTools → Console).
5. Network request details (DevTools → Network, failed request URL + status code).

## Testing

```bash
php artisan test
```

## Code Formatting

```bash
vendor/bin/pint
```


