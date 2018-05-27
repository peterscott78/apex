<h1>Create Notification</h1>

<e:form action="/admin/settings/notifications" enctype="multipart/form-data">
<input type="hidden" name="controller" value="~controller~">
<input type="hidden" name="sender" value="~sender~">
<input type="hidden" name="recipient" value="~recipient~">
<input type="hidden" name="condition_vars" value="~condition_vars~">

<e:box>
	<e:box_header title="Notification Details">
		<p>Below shows all general details regarding this notification.</p>
	</e:box_header>

	<e:function alias="notification_condition" controller="~controller~" condition_vars="~condition_vars~">
</e:box>

<e:box>
	<e:box_header title="Message Contents">
		<p>To complete creation of the new notification, enter the contents of the message below.</p>
	</e:box_header>

	<e:function alias="display_form" form="notification" controller="~controller~">
</e:box>

