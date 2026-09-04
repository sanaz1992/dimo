<x-Instagram::automation-rules.automation-rules-form :title="__('instagram::attributes.create_automation_rule')"
    :steps="$steps" :current-step="$currentStep" :tenants="$tenants" :instagram-accounts="$instagramAccounts"
    :instagram-posts="$instagramPosts" :match-types="$matchTypes" :trigger-types="$triggerTypes" />
