# AI INSTRUCTIONS #
There has been changes requested by the client regarding the system. So we aim to update the system to tailor their needs. 

---

## WORKFLOW ##
1. Admins can edit, delete, see users assigned with (admin, adviser, student) roles. They have full access to all users.
2. Advisers can see users assigned with (admin, adviser, student) roles. They can create users, but when creating a user, they can only assign the student role.

--- 

## TASK OVERVIEW ##
1. Merge the student table with the user table. Remove the council fk from the user table and add the bio field.
2. Remove the student resource and all other files referencing the old seperate student resource.
3. Due to these changes, you might need to copy the layout of the student form and apply it to the user form.
4. The users will now have these fields. Remove is_admin, council; Add bio, roles (admin, adviser, student).

## NOTES ##
1. This hasnt been pushed to production so you can change the exsisting migartion files accordingly.
2. If you cannot delete files, just give me the list of files I need to delete.