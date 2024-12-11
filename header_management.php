<nav>
    <div class="sidebar-button">
        <i class="bx bx-menu sidebarBtn"></i>
        <span class="dashboard">Pannel</span>
    </div>
    <div class="search-box">
        <input type="text" placeholder="Recherche..." />
        <i class="bx bx-search"></i>
    </div>
    <div class="profile-details" style="display: flex; align-items: center; background: #f5f6fa; border: 1px solid #ddd; border-radius: 6px; padding: 5px 15px; width: fit-content;">
        <?php if (!empty($user_info['photo'])): ?>
            <img src="pdp/<?php echo htmlspecialchars($user_info['photo']); ?>" alt="Profile Picture" class="rounded-circle" style="width: 40px; height: 40px; object-fit: cover; margin-right: 10px;">
        <?php endif; ?>
        <span class="admin_name" style="font-size: 14px; font-weight: 500; color: #333; white-space: nowrap;"><?php echo htmlspecialchars($user_info['Prenom'] . ' ' . $user_info['Nom']); ?></span>
    </div>
</nav>