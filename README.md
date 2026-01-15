## TASK ##
Instead of the current logic where users can only see their participating and assigned evaluation in the evaluation resource, I added a MyEvaluation resource whe it lists all the evaluations the user is assigned or is a part of. The evaluation resource should now be only accessed by the admin. 

Since the council adviser is the one responible for managing the student relation table, I also intend to remove the function reganding the student management from the evaluation resource and move it to the MyEvaluation resource.

The MyEvaluation should house the exact logic of the student relation. And should look exactly how the Evaluation view currenly looks like. This is were the adding of students and assigning peer evaluations should go.

I still want the StudentRelation to be present in the Evalution resource but without the function managing it so its just a read only. The peer evaluator infolist section is also not needed there so we can remove it. And can we revamp the dispalyed account information to match the layout of the text input of the evaluation form?

So in a nutshell, MyEvaluation is for managing evaluations assigned to the user while Evaluations for for admin creating evaluations and monitoring what is done.

## ISSUE
1. When I go to MyEvaluation as a student and go to view, the edit button is seen, when I click it, it shows forbidden which is good but UX wise, why pu that button in the first place when it lead me to forbidden page. The edit button should only be accessible to the council adviser

Solution: Only show the edit button to users assigned as council advisers.