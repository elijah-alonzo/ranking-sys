## TASK ##
Instead of the current logic where users can only see their participating and assigned evaluation in the evaluation resource, I added a MyEvaluation resource whe it lists all the evaluations the user is assigned or is a part of. The evaluation resource should now be only accessed by the admin. 

Since the council adviser is the one responible for managing the student relation table, I also intend to remove the function reganding the student management from the evaluation resource and move it to the MyEvaluation resource.

The MyEvaluation should house the exact logic of the student relation. And should look exactly how the Evaluation view currenly looks like. This is were the adding of students and assigning peer evaluations should go.

I still want the StudentRelation to be present in the Evalution resource but without the function managing it so its just a read only. The peer evaluator infolist section is also not needed there so we can remove it. And can we revamp the dispalyed account information to match the layout of the text input of the evaluation form?

So in a nutshell, MyEvaluation is for managing evaluations assigned to the user while Evaluations for for admin creating evaluations and monitoring what is done.

## ISSUE
1. Evaluation resource is accessible with all user types. It should only be accessible by the admin. I cna see the evaluation nav link when I login as an adviser and student. Also remove the constraint where students and advisers can only see evaluation assigned to them in this resource.
2. The evalutions made is not being shown in the My Evaluation table. It should show all evaluations assigned to and is participated by the USer. It should contain the same columns present in the evaluation table. 
3. In relation with number issue 2, there is a my evaluation and evaluation nav link for the admins which is correct. But when I assign an evaluation for that admin, it is not shown in the my evaluation. Users with the admin and adviser roles can be assigned as council advisers
4. I can also see an add new my evaluation buttton in the my evaluation list. Remove it