<x-Instagram::automation-rules.automation-rules-form :title="__('instagram::attributes.edit_automation_rule') . ' ' . ($this->automationRule ? ': ' . $this->automationRule->name : '')" :steps="$steps" :current-step="$currentStep"
    :automation-rule="$automationRule" :tenants="$tenants" :instagram-accounts="$instagramAccounts" :instagram-posts="$instagramPosts"
    :match-types="$matchTypes" :trigger-types="$triggerTypes" :action-types="$actionTypes" :action-form="$actionForm"/>
