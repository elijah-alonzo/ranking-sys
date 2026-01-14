# AI INSTRUCTIONS #
We need to make a way for users to edit their personal account.
---

## WORKFLOW ##
Every year, the students who are part of councils are evaluated for their performance.There is a new set of student officers every year.  The evaluation has three criteria: adviser, peer, and self. These criterias are computed to get the result that determines their rank. Adviser evaluation is filled up by the council adviser, Peer evaluators are students who are part of the council assigned to evaluate their fellow students, Self evaluation is where the student evluates themselves.

--- 

## TASK OVERVIEW ##
1. Add a StudentRelation table where advisers can add students to the council. When adding a student, a form will show whith the following fields: Student, Position, and a check box for Assign Peer Evaluatees. The ViewEvaluation should only display the details, while the edit is where the assigning happens.
2. Lets add the feature where we can add students to evaluation and assign peer evlautees to them.


## NOTES ##
1. I have done this before in another system of mine. I just need to apply that same feature here. I attached the folder that I used for my previous project for you to know how I want things to work. Just make the proper changes so that we can implement this in our new system.
2. In my old work, students and users are different. Students needs to login to the student panel to acces their evaluations. Also, in the old system the evaluation was for organizations not councils.
3. This new system I am developing works the same way as my old one, but this time there is no separate panels.
2. This hasnt been pushed to production so you can change the exsisting migartion files if needed accordingly.
3. If you cannot delete files, just give me the list of files I need to delete.