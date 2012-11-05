
<?= PHP_EOL ?>

<?php // ---------------------------------------------------- base url ----------------------------------------------------  ?>
 <urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
   <url>
     <loc><?= $central->karma_link_base ?></loc>
     <lastmod><?= $time ?></lastmod>
     <changefreq>monthly</changefreq>
     <priority>0.5</priority>
   </url>
<?php // ---------------------------------------------------- root menu items ----------------------------------------------------  ?>
    <url>
     <loc><?= $central->karma_link_base ?>friends</loc>
     <lastmod><?= $time ?></lastmod>
     <changefreq>monthly</changefreq>
     <priority>0.5</priority>
   </url> 
    <url>
     <loc><?= $central->karma_link_base ?>promoters</loc>
     <lastmod><?= $time ?></lastmod>
     <changefreq>monthly</changefreq>
     <priority>0.5</priority>
   </url> 
    <url>
     <loc><?= $central->karma_link_base ?>venues</loc>
     <lastmod><?= $time ?></lastmod>
     <changefreq>monthly</changefreq>
     <priority>0.5</priority>
   </url><?php if(false): ?>
   <url>
     <loc><?= $central->karma_link_base ?>corp</loc>
     <lastmod><?= $time ?></lastmod>
     <changefreq>monthly</changefreq>
     <priority>0.5</priority>
   </url>
   <url>
     <loc><?= $central->karma_link_base ?>corp/team</loc>
     <lastmod><?= $time ?></lastmod>
     <changefreq>monthly</changefreq>
     <priority>0.5</priority>
   </url><?php endif; ?>
<?php // ---------------------------------------------------- promoters ----------------------------------------------------  ?>
<?php // ---------------------------------------------------- promoters - cities ----------------------------------------------------  ?>
<?php foreach($all_cities_promoters as $city): ?>
    <url>
     <loc><?= $central->karma_link_base ?>promoters/<?= $city->url_identifier ?></loc>
     <lastmod><?= $time ?></lastmod>
     <changefreq>monthly</changefreq>
     <priority>0.5</priority>
   </url>
<?php endforeach; ?>
<?php // ---------------------------------------------------- promoters - individuals ----------------------------------------------------  ?>
<?php foreach($all_promoters as $promoter): ?>
    <url>
     <loc><?= $central->karma_link_base ?>promoters/<?= $promoter->c_url_identifier ?>/<?= str_replace(' ', '_', $promoter->up_public_identifier) ?></loc>
     <lastmod><?= $time ?></lastmod>
     <changefreq>monthly</changefreq>
     <priority>0.5</priority>
   </url>
   <url>
     <loc><?= $central->karma_link_base ?>promoters/<?= $promoter->c_url_identifier ?>/<?= str_replace(' ', '_', $promoter->up_public_identifier) ?>/guest_lists</loc>
     <lastmod><?= $time ?></lastmod>
     <changefreq>monthly</changefreq>
     <priority>0.5</priority>
   </url>
<?php // ---------------------------------------------------- promoters - guest_lists ----------------------------------------------------  ?>
   <?php foreach($promoter->guest_lists as $gl): ?>
   <url>
     <loc><?= $central->karma_link_base ?>promoters/<?= $promoter->c_url_identifier ?>/<?= str_replace(' ', '_', $promoter->up_public_identifier) ?>/guest_lists/<?= str_replace(' ', '_', $gl->pgla_name) ?></loc>
     <lastmod><?= $time ?></lastmod>
     <changefreq>monthly</changefreq>
     <priority>0.5</priority>
   </url>
   <?php endforeach; ?>
   <url>
     <loc><?= $central->karma_link_base ?>promoters/<?= $promoter->c_url_identifier ?>/<?= str_replace(' ', '_', $promoter->up_public_identifier) ?>/events</loc>
     <lastmod><?= $time ?></lastmod>
     <changefreq>monthly</changefreq>
     <priority>0.5</priority>
   </url>
<?php endforeach; ?>
<?php // ---------------------------------------------------- venues ----------------------------------------------------  ?>
<?php // ---------------------------------------------------- venues - cities ----------------------------------------------------  ?>
<?php foreach($all_cities_venues as $city): ?>
	<url>
     <loc><?= $central->karma_link_base ?>venues/<?= $city->url_identifier ?></loc>
     <lastmod><?= $time ?></lastmod>
     <changefreq>monthly</changefreq>
     <priority>0.5</priority>
   	</url>
<?php endforeach; ?>
<?php // ---------------------------------------------------- venues - individuals ----------------------------------------------------  ?>
<?php foreach($all_venues as $venue): ?>
	<url>
     <loc><?= $central->karma_link_base ?>venues/<?= $venue->c_url_identifier ?>/<?= str_replace(' ', '_', $venue->tv_name) ?></loc>
     <lastmod><?= $time ?></lastmod>
     <changefreq>monthly</changefreq>
     <priority>0.5</priority>
   </url>
   <url>
     <loc><?= $central->karma_link_base ?>venues/<?= $venue->c_url_identifier ?>/<?= str_replace(' ', '_', $venue->tv_name) ?>/guest_lists</loc>
     <lastmod><?= $time ?></lastmod>
     <changefreq>monthly</changefreq>
     <priority>0.5</priority>
   </url>
<?php // ---------------------------------------------------- promoters - guest_lists ----------------------------------------------------  ?>
   <?php foreach($venue->guest_lists as $gl): ?>
   <url>
     <loc><?= $central->karma_link_base ?>venues/<?= $venue->c_url_identifier ?>/<?= str_replace(' ', '_', $venue->tv_name) ?>/guest_lists/<?= str_replace(' ', '_', $gl->tgla_name) ?></loc>
     <lastmod><?= $time ?></lastmod>
     <changefreq>monthly</changefreq>
     <priority>0.5</priority>
   </url>
   <?php endforeach; ?>
   <url>
     <loc><?= $central->karma_link_base ?>venues/<?= $venue->c_url_identifier ?>/<?= str_replace(' ', '_', $venue->tv_name) ?>/events</loc>
     <lastmod><?= $time ?></lastmod>
     <changefreq>monthly</changefreq>
     <priority>0.5</priority>
   </url>
<?php endforeach; ?>
<?php // ---------------------------------------------------- users ----------------------------------------------------  ?>
<?php foreach($vc_friends as $user): ?>
   <url>
     <loc><?= $central->karma_link_base ?>friends/<?= $user->third_party_id ?></loc>
     <lastmod><?= $time ?></lastmod>
     <changefreq>weekly</changefreq>
     <priority>0.5</priority>
   </url>
<?php endforeach; ?>
</urlset>