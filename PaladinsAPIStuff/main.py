import pyrez
import json
import sys


class Match:
    def __init__(self, match_id):
        self.map = ""
        self.match_length = ""
        self.ban1 = ""
        self.ban2 = ""
        self.ban3 = ""
        self.ban4 = ""
        self.team1_score = ""
        self.team2_score = ""
        self.match_id = match_id

    def set_map(self, new_map):
        self.map = new_map.lstrip("Ranked ").strip()

    def set_match_length(self, new_match_length):
        match_len = int(new_match_length.strip())
        minutes = match_len // 60
        self.match_length = str(minutes) + " perc, " + str(match_len % 60) + " másodperc"

    def set_bans(self, new_ban1, new_ban2, new_ban3, new_ban4):
        self.ban1 = new_ban1.strip()
        self.ban2 = new_ban2.strip()
        self.ban3 = new_ban3.strip()
        self.ban4 = new_ban4.strip()

    def set_score(self, team1_score, team2_score, winner):
        if winner:
            self.team1_score = team1_score if team1_score > team2_score else team2_score
            self.team2_score = team2_score if team1_score > team2_score else team1_score
        else:
            self.team1_score = team2_score if team1_score > team2_score else team1_score
            self.team2_score = team1_score if team1_score > team2_score else team2_score

    def append_to_json(self, where):
        match_data = dict()
        match_data["map"] = self.map
        match_data["match_length"] = self.match_length
        match_data["ban1"] = self.ban1
        match_data["ban2"] = self.ban2
        match_data["ban3"] = self.ban3
        match_data["ban4"] = self.ban4
        match_data["team1score"] = self.team1_score
        match_data["team2score"] = self.team2_score
        if where == "output":
            json_obj = json.dumps(match_data, indent=4, ensure_ascii=False)
            with open("PaladinsAPIStuff/output_match.json", "a", encoding="UTF-8") as file:
                file.write(json_obj)
        else:
            match_data["match_id"] = self.match_id
            json_obj = json.dumps(match_data, indent=4, ensure_ascii=False)
            with open("PaladinsAPIStuff/output_log.json", "a", encoding="UTF-8") as file:
                file.write(json_obj)


class Player:
    def __init__(self):
        self.name = ""
        self.champion = ""
        self.kills = ""
        self.deaths = ""
        self.assists = ""
        self.damage_done = ""
        self.damage_taken = ""
        self.healing_done = ""
        self.shielding_done = ""
        self.objective_time = ""
        self.talent = ""

    def set_name(self, new_name):
        self.name = new_name.encode("utf-8").decode("utf-8").strip()

    def set_champion(self, new_champion):
        if new_champion.strip() == "Mal'Damba":
            self.champion = "MalDamba"
        else:
            self.champion = new_champion.strip()

    def set_kills(self, new_kills):
        self.kills = new_kills.strip()

    def set_deaths(self, new_deaths):
        self.deaths = new_deaths.strip()

    def set_assists(self, new_assists):
        self.assists = new_assists.strip()

    def set_damage(self, damage_done):
        self.damage_done = damage_done.strip()

    def set_damage_taken(self, damage_taken):
        self.damage_taken = damage_taken.strip()

    def set_healing(self, healing_done):
        self.healing_done = healing_done.strip()

    def set_shielding(self, shielding_done):
        self.shielding_done = shielding_done.strip()

    def set_ot(self, objective_time):
        self.objective_time = objective_time.strip()

    def set_talent(self, talent):
        self.talent = talent.strip()

    def __str__(self):
        return self.name + " -> " + self.champion.strip() + " -> " + self.kills.strip() + "/" + self.deaths.strip() + "/" + self.assists.strip()\
               + " -> " + self.damage_done.strip() + " -> " + self.damage_taken.strip() + " -> " + self.healing_done.strip() + " -> " + self.shielding_done.strip() + " -> " + self.objective_time.strip()\
               + " -> " + self.talent

    def append_to_json(self, i):
        player_data = dict()
        player_data["name"] = self.name.encode("utf-8").decode("utf-8")
        player_data["champion"] = self.champion.strip()
        player_data["kda"] = self.kills.strip() + "/" + self.deaths.strip() + "/" + self.assists.strip()
        player_data["damage"] = self.damage_done
        player_data["damage_taken"] = self.damage_taken
        player_data["healing"] = self.healing_done
        player_data["shielding"] = self.shielding_done
        player_data["ot"] = self.objective_time
        player_data["talent"] = self.talent
        json_obj = json.dumps(player_data, indent=4, ensure_ascii=False)
        with open("PaladinsAPIStuff/output_players.json", "a", encoding="UTF-8") as file:
            file.write("\"player" + str(i) + "\":[")
            file.write(json_obj)
            file.write("]")


# playerid = 438226
# '''input("Írd be a matchID-t: ")'''

devId = 3545
authKey = 'B1B0831987444E548ED710B76C9254F9'

matchid = int(sys.argv[1])

with pyrez.PaladinsAPI(devId, authKey) as paladins:
    # print(paladins.getMatch(matchid))
    data = str(paladins.getMatch(matchid))

with open('PaladinsAPIStuff/data.txt', 'w') as outfile:
    outfile.write(data)

players = list()
indices = list()

match = Match(matchid)
bans = list()
scores = list()
taskforce = None

for i in range(0, 10):
    players.append(Player())

for i in range(0, 11):
    indices.append(0)

with open("PaladinsAPIStuff/data.txt", "r") as infile:
    for line in infile:
        if "\"Map_Game\":" in line:
            _map = line.strip()
            _map = _map.replace("\"Map_Game\":", "")
            _map = _map.replace(",", "")
            _map = _map.replace("\"", "")
            match.set_map(_map)

        if "\"Match_Duration\":" in line:
            dur = line.strip()
            dur = dur.replace("\"Match_Duration\":", "")
            dur = dur.replace(",", "")
            dur = dur.replace("\"", "")
            match.set_match_length(dur)

        if "\"Ban_1\":" in line:
            if len(bans) < 4:
                ban = line.strip()
                ban = ban.replace("\"Ban_1\":", "")
                ban = ban.replace(",", "")
                ban = ban.replace("\"", "")
                bans.append(ban)

        if "\"Ban_2\":" in line:
            if len(bans) < 4:
                ban = line.strip()
                ban = ban.replace("\"Ban_2\":", "")
                ban = ban.replace(",", "")
                ban = ban.replace("\"", "")
                bans.append(ban)

        if "\"Ban_3\":" in line:
            if len(bans) < 4:
                ban = line.strip()
                ban = ban.replace("\"Ban_3\":", "")
                ban = ban.replace(",", "")
                ban = ban.replace("\"", "")
                bans.append(ban)
                
        if "\"Ban_4\":" in line:
            if len(bans) < 4:
                ban = line.strip()
                ban = ban.replace("\"Ban_4\":", "")
                ban = ban.replace(",", "")
                ban = ban.replace("\"", "")
                bans.append(ban)

        if "\"Team1Score\":" in line:
            if len(scores) < 2:
                score = line.strip()
                score = score.replace("\"Team1Score\":", "")
                score = score.replace(",", "")
                score = score.replace("\"", "")
                scores.append(score)

        if "\"Team2Score\":" in line:
            if len(scores) < 2:
                score = line.strip()
                score = score.replace("\"Team2Score\":", "")
                score = score.replace(",", "")
                score = score.replace("\"", "")
                scores.append(score)

        if "\"playerName\":" in line:
            name = line.strip()
            name = name.replace("\\u00f6", "ö")
            name = name.replace("\\u00e9", "é")
            name = name.replace("\"playerName\": ", "")
            name = name.replace(",", "")
            name = name.replace("\"", "")
            players[indices[0]].set_name(name)
            indices[0] += 1
            #print(name)

        if "\"Damage_Player\":" in line:
            damage = line.strip()
            damage = damage.replace("\"Damage_Player\":", "")
            damage = damage.replace("\"", "")
            damage = damage.replace(",", "")
            players[indices[1]].set_damage(damage)
            indices[1] += 1
            #print("Damage: " + damage)

        if "\"Damage_Taken\":" in line:
            damage_taken = line.strip()
            damage_taken = damage_taken.replace("\"Damage_Taken\":", "")
            damage_taken = damage_taken.replace("\"", "")
            damage_taken = damage_taken.replace(",", "")
            players[indices[10]].set_damage_taken(damage_taken)
            indices[10] += 1
            # print("Damage: " + damage)

        if "\"Healing\":" in line:
            healing = line.strip()
            healing = healing.replace("\"Healing\":", "")
            healing = healing.replace("\"", "")
            healing = healing.replace(",", "")
            players[indices[2]].set_healing(healing)
            indices[2] += 1
            #print("Healing: " + healing)

        if "\"Objective_Assists\":" in line:
            ot = line.strip()
            ot = ot.replace("\"Objective_Assists\":", "")
            ot = ot.replace("\"", "")
            ot = ot.replace(",", "")
            players[indices[3]].set_ot(ot)
            indices[3] += 1
            #print("Objective Time: " + ot)

        if "\"Damage_Mitigated\":" in line:
            shielding = line.strip()
            shielding = shielding.replace("\"Damage_Mitigated\":", "")
            shielding = shielding.replace("\"", "")
            shielding = shielding.replace(",", "")
            players[indices[4]].set_shielding(shielding)
            indices[4] += 1
            #print("Shielding: " + shielding)

        if "\"Assists\":" in line:
            #print("-------------")
            assists = line.strip()
            assists = assists.replace("\"Assists\":", "")
            assists = assists.replace("\"", "")
            assists = assists.replace(",", "")
            players[indices[5]].set_assists(assists)
            indices[5] += 1
            #print("Assists: " + assists)

        if "\"Deaths\":" in line:
            deaths = line.strip()
            deaths = deaths.replace("\"Deaths\":", "")
            deaths = deaths.replace("\"", "")
            deaths = deaths.replace(",", "")
            players[indices[6]].set_deaths(deaths)
            indices[6] += 1
            #print("Deaths: " + deaths)

        if "\"Kills_Player\":" in line:
            kills = line.strip()
            kills = kills.replace("\"Kills_Player\":", "")
            kills = kills.replace("\"", "")
            kills = kills.replace(",", "")
            players[indices[7]].set_kills(kills)
            indices[7] += 1
            #print("Kills: " + kills)

        if "\"Reference_Name\":" in line:
            champion = line.strip()
            champion = champion.replace("\"Reference_Name\":", "")
            champion = champion.replace("\"", "")
            champion = champion.replace(",", "")
            players[indices[8]].set_champion(champion)
            indices[8] += 1
            #print("Champion: " + champion)

        if "\"Item_Purch_6\":" in line:
            talent = line.strip()
            talent = talent.replace("\"Item_Purch_6\":", "")
            talent = talent.replace("\"", "")
            talent = talent.replace(",", "")
            players[indices[9]].set_talent(talent)
            indices[9] += 1

        if "\"TaskForce\":" in line and taskforce is None:
            taskforce = line.strip()
            taskforce = taskforce.replace("\"TaskForce\":", "")
            taskforce = taskforce.replace("\"", "")
            taskforce = taskforce.replace(",", "")
            taskforce = int(taskforce)


for i in range(0, 10):
    print(str(players[i]).encode("utf-8").decode("utf-8"))

new_file_match = open("PaladinsAPIStuff/output_match.json", "w", encoding="UTF-8").close()
new_file_players = open("PaladinsAPIStuff/output_players.json", 'w', encoding="UTF-8")
new_file_players.write("{\n")
new_file_players.close()
if taskforce == 1:
    for i in range(0, 10):
        players[i].append_to_json(i + 1)
        with open("PaladinsAPIStuff/output_players.json", "a", encoding="UTF-8") as file:
            if i != 9:
                file.write(",")
            else:
                file.write("}")
    match.set_score(*scores, True)
    bans_new = [bans[1], bans[3], bans[0], bans[2]]
else:
    for i in range(5, 10):
        players[i].append_to_json(i + 1 - 5)
        with open("PaladinsAPIStuff/output_players.json", "a", encoding="UTF-8") as file:
            file.write(",")
    for i in range(0, 5):
        players[i].append_to_json(i + 1 + 5)
        with open("PaladinsAPIStuff/output_players.json", "a", encoding="UTF-8") as file:
            if i != 4:
                file.write(",")
            else:
                file.write("}")
    match.set_score(*scores, False)
    bans_new = [bans[0], bans[2], bans[1], bans[3]]

match.set_bans(*bans_new)
match.append_to_json("output")
match.append_to_json("log")

