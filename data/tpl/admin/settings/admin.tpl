
<h1>Administrators</h1>

<e:form>

<e:tab_control>

	<e:tab_page name="Existing Administrators">

		<e:box_header title="Existing Administrators">
			<p>The below table lists all existing administrator account that you may manage or delete.</p>
		</e:box_header>

		<e:function alias="display_table" table="admin">
	</e:tab_page>

	<e:tab_page name="Create New Administrator">

		<e:box_header title="Create New Administrator">
			<p>You may create a new administrator account by completing the below form with the desired information.</p>
		</e:box_header>

		<e:function alias="display_form" form="admin">
	</e:tab_page>

</e:tab_control>

