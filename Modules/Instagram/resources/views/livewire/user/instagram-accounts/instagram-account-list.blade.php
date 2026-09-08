<x-Instagram::instagram-accounts.instagram-accounts-table
    :title="__('instagram::attributes.my_instagram_accounts_list')" :instagram-accounts="$instagramAccounts"
    :conversations-route-name="'user.instagram_accounts.conversations.index'"
    :instagram-posts-list-route-name="'user.instagram_posts.index'"
    :automation-runs-list-route-name="'user.automation_runs.index'" />
