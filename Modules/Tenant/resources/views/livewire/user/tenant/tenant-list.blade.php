<x-Tenant::tenant.tenant-table :title="__('tenant::attributes.my_tenants_list')" :tenants="$tenants" :create-route-name="'user.tenants.create'"
    :edit-route-name="'user.tenants.edit'" :instagram-route-name="'user.instagram_accounts.index'"
    :can-edit-status="false" />