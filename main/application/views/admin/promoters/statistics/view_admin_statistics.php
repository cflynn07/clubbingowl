<?php Kint::dump($piwik_stats); ?>

<hr /> 
		  
<h1>Tabs and statistics</h1> 
<div style="clearboth"></div> 
 
	<div class="tabs" style="width:870px;"> 
	<div class="ui-widget-header"> 
		<span>Fede's Promoter Statistics</span> 
		<ul> 
			<li><a href="#tabs-1">Lines</a></li> 
			<li><a href="#tabs-2">Bars</a></li> 
			<li><a href="#tabs-3">Area</a></li> 
		</ul> 
	</div> 

	<div id="tabs-1"> 
		<table class="stats line">
			
			<thead> 
				<tr> 
				<td></td>
					<th scope="col"><?= date('Y-m-d', strtotime('-6 day', time())) ?></th>
					<th scope="col"><?= date('Y-m-d', strtotime('-5 day', time())) ?></th>
					<th scope="col"><?= date('Y-m-d', strtotime('-4 day', time())) ?></th>
					<th scope="col"><?= date('Y-m-d', strtotime('-3 day', time())) ?></th>
					<th scope="col"><?= date('Y-m-d', strtotime('-2 day', time())) ?></th>
					<th scope="col"><?= date('Y-m-d', strtotime('-1 day', time())) ?></th>
					<th scope="col"><?= date('Y-m-d', time()) ?></th>
				</tr>
			</thead>
 
								<tbody> 
				<tr> 
					<th scope="row">Page views</th>
					<?php foreach($piwik_stats as $key => $value): ?>
						<td><?=$value?></td>
					<?php endforeach; ?>
				</tr> 
			</tbody> 
		</table> 
	</div> <!-- end of first tab --> 
	<div id="tabs-2"> 
		<table class="stats bar"> 
			<thead> 
			  <tr> 
				<td></td> 
				<th scope="col">01.12</th> 
				<th scope="col">02.12</th> 
				<th scope="col">03.12</th> 
				<th scope="col">04.12</th> 
				<th scope="col">05.12</th> 
				<th scope="col">06.12</th> 
				<th scope="col">07.12</th> 
				<th scope="col">08.12</th> 
				<th scope="col">09.12</th> 
				<th scope="col">10.12</th> 
				<th scope="col">11.12</th> 
				<th scope="col">12.12</th> 
				<th scope="col">13.12</th> 
				<th scope="col">14.12</th> 
			  </tr> 
			</thead> 
 
								<tbody> 
			  <tr> 
				<th scope="row">Page views</th> 
				<td>10</td> 
				<td>37</td> 
				<td>81</td> 
				<td>121</td> 
				<td>124</td> 
				<td>148</td> 
				<td>112</td> 
				<td>200</td> 
				<td>130</td> 
				<td>192</td> 
				<td>40</td> 
				<td>70</td> 
				<td>20</td> 
				<td>60</td> 
			  </tr> 
			  <tr> 
				<th scope="row">Subscribers</th> 
				<td>3</td> 
				<td>5</td> 
				<td>15</td> 
				<td>20</td> 
				<td>18</td> 
				<td>30</td> 
				<td>23</td> 
				<td>17</td> 
				<td>5</td> 
				<td>9</td> 
				<td>13</td> 
				<td>15</td> 
				<td>11</td> 
				<td>14</td> 
			  </tr> 
			</tbody> 
		</table> 
	</div> 
	
	<div id="tabs-3"> 
		<table class="stats area"> 
			<thead> 
			  <tr> 
				<td></td> 
				<th scope="col">01.12</th> 
				<th scope="col">02.12</th> 
				<th scope="col">03.12</th> 
				<th scope="col">04.12</th> 
				<th scope="col">05.12</th> 
				<th scope="col">06.12</th> 
				<th scope="col">07.12</th> 
				<th scope="col">08.12</th> 
				<th scope="col">09.12</th> 
				<th scope="col">10.12</th> 
				<th scope="col">11.12</th> 
				<th scope="col">12.12</th> 
				<th scope="col">13.12</th> 
				<th scope="col">14.12</th> 
			  </tr> 
			</thead> 
 
								<tbody> 
			  <tr> 
				<th scope="row">Page views</th> 
				<td>10</td> 
				<td>37</td> 
				<td>81</td> 
				<td>121</td> 
				<td>124</td> 
				<td>148</td> 
				<td>112</td> 
				<td>200</td> 
				<td>130</td> 
				<td>192</td> 
				<td>40</td> 
				<td>70</td> 
				<td>20</td> 
				<td>60</td> 
			  </tr> 
			  <tr> 
				<th scope="row">Subscribers</th> 
				<td>3</td> 
				<td>5</td> 
				<td>15</td> 
				<td>20</td> 
				<td>18</td> 
				<td>30</td> 
				<td>23</td> 
				<td>17</td> 
				<td>5</td> 
				<td>9</td> 
				<td>13</td> 
				<td>15</td> 
				<td>11</td> 
				<td>14</td> 
			  </tr> 
			</tbody> 
		</table> 
	</div> 
</div> 
<div class="clearboth"></div> 