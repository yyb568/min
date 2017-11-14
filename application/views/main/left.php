<nav class="navbar-default navbar-static-side" role="navigation">
	<div class="nav-close">
		<i class="fa fa-times-circle"></i>
	</div>
	<div class="sidebar-collapse">
		<ul class="nav" id="side-menu">
			<li class="nav-header">
				<div class="dropdown profile-element">
					<span><img alt="image" class="img-circle"
						src="" height="64" width="64" /></span> <a
						data-toggle="dropdown" class="dropdown-toggle" href="#"> <span
						class="clear"> <span class="block m-t-xs"></span> <span
							class="text-muted text-xs block"><?=$uname?><b class="caret"></b></span>
					</span>
					</a>
					<ul class="dropdown-menu animated fadeInRight m-t-xs">
						<li><a href="<?=site_url("login/outlogin")?>">安全退出</a></li>
					</ul>
				</div>
				<div class="logo-element">ASDF</div>
			</li>
			<?php foreach((array)$parentmenuList as $key => $val){ ?>
			<li>
			<a href="#"><i class="<?=$val['icon']?>"></i> <span class="nav-label"><?=$val['menu_name']?></span><span class="fa arrow"></span></a>
				<ul class="nav nav-second-level">
					<?php foreach((array)$lastmenulist[$val['id']] as $k => $v){?>
							<li><a class="J_menuItem" href="<?=site_url($v['url'])?>" data-index="0"><?=$v['menu_name']?></a></li>
					<?php }?>
				</ul>
			</li>
			<?php } ?>

		</ul>
	</div>
</nav>