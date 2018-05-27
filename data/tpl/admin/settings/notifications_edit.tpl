
<h1>Edit Notification</h1>

<e:form action="/admin/settings/notifications" enctype="multipart/form-data">

<e:box>
	<e:box_header title="Notification Details">
		<p>Below shows all general details regarding this notification.</p>
	</e:box_header>

	<e:function alias="notification_condition"condition_vars="~condition_vars~" notification_id="~notification_id~">
</e:box>

<e:box>
	<e:box_header title="Message Contents">
	<p>Below shows the current contents of the selected e-mail notification, which you may modify as desired.</p>	 o0	
	</e:box_header>

	<e:function alias="display_form" form="notification" record_id="~notification_id~" >
</e:box>



